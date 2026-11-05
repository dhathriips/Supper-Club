"""Run against a fresh disposable install. Python 3 standard library only.
Set SUPPER_BASE, SUPPER_PHP and the app's database environment as needed.
This test creates fictional users/orders and exercises admin CRUD.
"""
import http.cookiejar, os, re, subprocess, urllib.error, urllib.parse, urllib.request
from pathlib import Path
from secrets import randbelow

ROOT = Path(__file__).resolve().parents[1]
BASE = os.getenv('SUPPER_BASE', 'http://127.0.0.1:8000/')
assert urllib.parse.urlparse(BASE).hostname in ('localhost', '127.0.0.1', '::1')
class Browser:
    def __init__(self):
        self.cookies = http.cookiejar.CookieJar()
        self.client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(self.cookies))
        self.csrf = ''
    def request(self, page, form=None):
        data = urllib.parse.urlencode(form).encode() if form is not None else None
        try:
            res = self.client.open(BASE+'?p='+page, data, timeout=10)
        except urllib.error.HTTPError as ex:
            res = ex
        self.status = res.status
        self.url = res.geturl()
        body = res.read().decode()
        match = re.search(r'name="csrf" value="([a-f0-9]+)"', body)
        if match: self.csrf = match[1]
        assert self.status < 500, (page, body[:600])
        return body
    def post(self, page, action, **fields):
        return self.request(page, dict(csrf=self.csrf, action=action, **fields))

def check(condition, label):
    if not condition: raise AssertionError(label)
    print('PASS', label, flush=True)

def account(browser, n):
    email = f'taster{n}@example.test'
    fields = dict(name='Demo Taster', email=email, student_code=f'SC-{n:04}', password='Kitchen123')
    browser.request('register')
    check('ready' in browser.post('register','register',**fields),'register fictional user')
    browser.post('login','login',email=email,password='Kitchen123')
    return email

a, b = Browser(), Browser()
check('Kitchen picks' in a.request('home'), 'home and database seed')
check('Slice Society' in a.request('discover&q=margherita') and 'Ghee &amp; Grain' not in a.request('discover&q=margherita'), 'search by dish')
check('No kitchens found' in a.request('discover&q=%25'), 'literal wildcard search')
check('id="board"' in a.request('lab'),'interaction lab')
check('Sign in' in a.request('orders') and 'p=login' in a.url,'guest order protection')
a.request('menu&id=1')
check('added to your bag' in a.post('menu&id=1','add',id=1),'guest add to bag')
check('another kitchen' in a.post('menu&id=2','add',id=4),'single-kitchen constraint')
a.post('cart','quantity',id=1,qty=2)
check('₹190' in a.request('cart'),'two portions at 95 rupees')
a.post('cart','checkout',quoted=190)
check('p=login' in a.url,'checkout requests login')
invalid = dict(name='Demo Taster', email='wrong@invalid.org', student_code='SC-0034', password='Kitchen123')
check('@example.test' in a.post('register','register',**invalid) and 'role="alert"' in a.post('register','register',**invalid),'server rejects invalid registration')
n=randbelow(4000)+1000
email=account(a,n)
check('p=cart' in a.url,'resume bag after login')
check('Prices changed' in a.post('cart','checkout',quoted=1),'tampered price rejected')
receipt=a.post('cart','checkout',quoted=190)
check('confirmed' in receipt and 'Benne dosa' in receipt and '₹190' in receipt,'mock order saved with line items')
check('Your bag is empty' in a.request('cart'),'bag cleared')
a.request('admin')
check(a.status==403,'customer cannot open admin')
a.request('feedback')
notes=a.post('feedback','note-add',body='Test <spice>')
check('Test &lt;spice&gt;' in notes,'file create/read and escaping')
note=re.search(r'name="id" value="([0-9a-f]{16})"',notes)[1]
check('Revised note text' in a.post('feedback','note-edit',id=note,body='Revised note text'),'file update')
account(b,n+4000)
check('No orders yet' in b.request('orders'),'order ownership')
check('unavailable' in b.post('feedback','note-delete',id=note),'feedback ownership')
check('Revised note text' not in a.post('feedback','note-delete',id=note),'file delete')
a.request('feedback',dict(csrf='bad',action='note-add',body='Rejected'))
check(a.status==403,'CSRF rejection')
a.post('feedback','logout')
check('p=login' in (a.request('feedback') and a.url),'logout destroys authentication')
check('First visit' not in a.request('home'),'last-visit cookie displayed on new session')

subprocess.run([os.getenv('SUPPER_PHP','php'),str(ROOT/'bin/make-admin.php'),email],check=True)
a.request('login')
a.post('login','login',email=email,password='Kitchen123')
a.request('admin')
kitchen=dict(name='Test Kitchen',cuisine='Test',area='Lab',rating='4.2',minutes=20,art='rice',tagline='Disposable kitchen')
check('Catalogue updated' in a.post('admin','admin-restaurant',id='',**kitchen),'admin create restaurant')
discover=a.request('discover&q=Test+Kitchen')
rid=re.search(r'href="\?p=menu&amp;id=(\d+)"',discover)[1]
a.request('admin')
check('Updated kitchen' in a.post('admin','admin-restaurant',id=rid,**dict(kitchen,tagline='Updated kitchen')),'admin update restaurant')
dish=dict(name='Demo dish',description='Sample plate',price=110,restaurant_id=rid,veg=1,available=1)
a.post('admin','admin-dish',id='',**dish)
menu=a.request('menu&id='+rid)
mid=re.search(r'name="id" value="(\d+)"',menu)[1]
check('Demo dish' in menu,'admin create dish visible in menu')
a.post('menu&id='+rid,'add',id=mid)
a.request('admin')
del dish['available']
a.post('admin','admin-dish',id=mid,**dish)
check('cannot be ordered' in a.post('cart','checkout',quoted=110),'availability rechecked at checkout')
a.post('admin','admin-dish',id=mid,**dict(dish,available=1))
check('confirmed' in a.post('cart','checkout',quoted=110),'updated dish orderable')
a.post('admin','admin-delete-dish',id=mid)
check('Demo dish' not in a.request('menu&id='+rid),'admin delete dish')
a.post('admin','admin-delete-restaurant',id=rid)
a.request('menu&id='+rid)
check(a.status==404,'admin delete restaurant')
check('Demo dish' in a.request('orders'),'receipt survives catalogue deletion')

