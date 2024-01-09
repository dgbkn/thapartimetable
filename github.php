<?php

require 'vendor/autoload.php'; // Load the Composer's autoloader

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;




// GitHub credentials
$githubToken = getenv('accessToken');
$githubUsername = 'dgbkn';
$repositoryName = 'thapartimedata';


// Local folder path to push to GitHub
$localFolderPath = 'timetable';



function createOrUpdateGitHubRepository($token, $username, $repoName)
{
    $client = new Client();

    try {
        // Check if the repository already exists
        $response = $client->get("https://api.github.com/repos/{$username}/{$repoName}", [
            'headers' => [
                'Authorization' => 'token ' . $token,
                'Accept' => 'application/vnd.github.v3+json',
            ],
        ]);

        // Repository exists, return its data
        return json_decode($response->getBody(), true);
    } catch (ClientException $e) {
        // Repository doesn't exist, create a new one
        $response = $client->post("https://api.github.com/user/repos", [
            'headers' => [
                'Authorization' => 'token ' . $token,
                'Accept' => 'application/vnd.github.v3+json',
            ],
            'json' => [
                'name' => $repoName,
                'private' => true, // Set this to false for public repository
            ],
        ]);

        // Return the newly created repository data
        return json_decode($response->getBody(), true);
    }
}

// Function to recursively create a tree of files and subdirectories
function createTreeRecursively($basePath, $localPath)
{
    $tree = [];

    foreach (scandir($basePath . DIRECTORY_SEPARATOR . $localPath) as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $itemPath = $localPath . DIRECTORY_SEPARATOR . $item;
        $fullPath = $basePath . DIRECTORY_SEPARATOR . $itemPath;

        if (is_dir($fullPath)) {
            // If the item is a directory, create a tree for its content
            $subTree = createTreeRecursively($basePath, $itemPath);
            $tree[] = [
                'path' => ltrim($itemPath, '/'), // Remove leading slash from path
                'mode' => '040000', // Directory mode
                'type' => 'tree', // Tree type for directories
                'sha' => $subTree['sha'],
            ];
        } else {
            // If the item is a file, add it to the tree
            $content = file_get_contents($fullPath);
            $tree[] = [
                'path' => ltrim($itemPath, '/'), // Remove leading slash from path
                'mode' => '100644', // Regular file mode
                'type' => 'blob', // Blob type for files
                'content' => $content,
            ];
        }
    }

    return createTree($tree);
}

// Function to create a new tree using GitHub API
function createTree($tree)
{
    global $githubToken, $githubUsername, $repositoryName;

    $client = new Client();

    $response = $client->post("https://api.github.com/repos/{$githubUsername}/{$repositoryName}/git/trees", [
        'headers' => [
            'Authorization' => 'token ' . $githubToken,
            'Accept' => 'application/vnd.github.v3+json',
        ],
        'json' => [
            'base_tree' => null,
            'tree' => $tree,
        ],
    ]);

    return json_decode($response->getBody(), true);
}

// Function to create a new commit using GitHub API
function createCommit($tree)
{
    global $githubToken, $githubUsername, $repositoryName;

    $client = new Client();

    $response = $client->post("https://api.github.com/repos/{$githubUsername}/{$repositoryName}/git/commits", [
        'headers' => [
            'Authorization' => 'token ' . $githubToken,
            'Accept' => 'application/vnd.github.v3+json',
        ],
        'json' => [
            'message' => 'Initial commit',
            'tree' => $tree['sha'],
            'parents' => [],
        ],
    ]);

    return json_decode($response->getBody(), true);
}

// Function to update the reference to the commit in the main branch (force push)
function updateBranchReference($commit)
{
    global $githubToken, $githubUsername, $repositoryName;

    $client = new Client();

    $client->post("https://api.github.com/repos/{$githubUsername}/{$repositoryName}/git/refs/heads/main", [
        'headers' => [
            'Authorization' => 'token ' . $githubToken,
            'Accept' => 'application/vnd.github.v3+json',
        ],
        'json' => [
            'sha' => $commit['sha'],
            'force' => true, // Perform a force push
        ],
    ]);
}

// Main code
$repoData = createOrUpdateGitHubRepository($githubToken, $githubUsername, $repositoryName);

$tree = createTreeRecursively($localFolderPath, '');

$commit = createCommit($tree);

updateBranchReference($commit);




?>
