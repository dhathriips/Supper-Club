<?php
// Readers and writers lock the same sidecar. Publish complete JSON via a same-directory rename.
function feedback(string $op = 'read', string $id = '', string $body = ''): array {
    $file = __DIR__.'/../storage/feedback.txt';
    $lock = fopen($file.'.lock','c');
    if (!$lock || !flock($lock, $op === 'read' ? LOCK_SH : LOCK_EX)) throw new RuntimeException('Feedback lock failed.');
    try {
        $records = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        $owner = (int)user()['id'];
        if (in_array($op, ['add','edit'], true)) bounded($body, 600, 'Feedback');
        if ($op === 'add') {
            $records[] = ['id'=>bin2hex(random_bytes(8)), 'owner'=>$owner, 'body'=>$body, 'date'=>date('Y-m-d H:i')];
        } elseif ($op !== 'read') {
            $i = array_search($id, array_column($records,'id'), true);
            if ($i === false || $records[$i]['owner'] !== $owner) fail('This note is unavailable.');
            if ($op === 'edit') $records[$i]['body'] = $body;
            elseif ($op === 'delete') array_splice($records,$i,1);
            else fail('Unknown feedback action.');
        }
        if ($op !== 'read') {
            $tmp = tempnam(dirname($file),'note-');
            try {
                $data = json_encode($records, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
                if (file_put_contents($tmp,$data) !== strlen($data) || !rename($tmp,$file)) throw new RuntimeException('Feedback write failed.');
            } finally { if (is_file($tmp)) unlink($tmp); }
        }
        return array_reverse(array_filter($records,fn($r)=>$r['owner'] === $owner));
    } finally { flock($lock,LOCK_UN); fclose($lock); }
}

