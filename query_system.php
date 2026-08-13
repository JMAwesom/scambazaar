<?php
declare(strict_types=1);

/**
 * Bazaar Query Storage System
 */

function query_directory(): string
{
    return __DIR__ . '/queries';
}

function query_file_path(): string
{
    return query_directory() . '/queries.json';
}

function ensure_query_storage(): void
{
    $directory = query_directory();

    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    $file = query_file_path();

    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }

    $htaccess = $directory . DIRECTORY_SEPARATOR . '.htaccess';

    if (!file_exists($htaccess)) {
        @file_put_contents($htaccess, "Require all denied\n");
    }
}

function load_queries(): array
{
    ensure_query_storage();

    $file = query_file_path();

    if (!file_exists($file)) {
        return [];
    }

    $handle = fopen($file, 'r');

    if (!$handle) {
        return [];
    }

    flock($handle, LOCK_SH);
    $content = stream_get_contents($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    $data = json_decode($content ?: '[]', true);

    return is_array($data) ? $data : [];
}

function save_queries(array $queries): bool
{
    ensure_query_storage();

    $file = query_file_path();

    $handle = fopen($file, 'c');

    if (!$handle) {
        return false;
    }

    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return false;
    }

    ftruncate($handle, 0);
    rewind($handle);

    fwrite(
        $handle,
        json_encode(
            $queries,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        )
    );

    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return true;
}

function generate_ticket_id(): string
{
    $queries = load_queries();

    do {
        $id = 'BZ-' . strtoupper(substr(bin2hex(random_bytes(6)), 0, 8));

        $exists = false;

        foreach ($queries as $query) {
            if (($query['id'] ?? '') === $id) {
                $exists = true;
                break;
            }
        }

    } while ($exists);

    return $id;
}

function add_query(string $name, string $email, string $message): array
{
    $queries = load_queries();

    $query = [
        'id' => generate_ticket_id(),
        'date' => gmdate('c'),
        'updated' => gmdate('c'),
        'name' => $name,
        'email' => $email,
        'message' => $message,
        'status' => 'pending',
        'responses' => []
    ];

    array_unshift($queries, $query);

    save_queries($queries);

    return $query;
}

function find_query_by_id(string $id): ?array
{
    $queries = load_queries();

    foreach ($queries as $query) {
        if (($query['id'] ?? '') === $id) {
            return $query;
        }
    }

    return null;
}

function find_query_by_id_and_email(string $id, string $email): ?array
{
    $query = find_query_by_id($id);

    if (!$query) {
        return null;
    }

    $queryEmail = trim((string)($query['email'] ?? ''));
    $inputEmail = trim($email);

    if (strcasecmp($queryEmail, $inputEmail) === 0) {
        return $query;
    }

    return null;
}

function add_query_response(string $id, string $message, string $sender = 'Admin'): bool
{
    $queries = load_queries();

    foreach ($queries as &$query) {
        if (($query['id'] ?? '') === $id) {
            $query['responses'][] = [
                'date' => gmdate('c'),
                'sender' => $sender,
                'message' => $message
            ];

            $query['status'] = 'answered';
            $query['updated'] = gmdate('c');

            return save_queries($queries);
        }
    }

    return false;
}
