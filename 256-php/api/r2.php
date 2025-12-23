<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/env.php';

use Aws\S3\S3Client;

// $access_key_id = $_ENV["R2_ACCESS_KEY_ID"];
// $access_key_secret = $_ENV["R2_ACCESS_KEY_SECRET"];

// $credentials = new Aws\Credentials\Credentials($access_key_id, $access_key_secret);

// $options = [
//     'region' => 'auto',
//     'endpoint' => "https://352fe5bb7c600168419ece2b27337991.r2.cloudflarestorage.com",
//     'version' => 'latest',
//     'credentials' => $credentials,
//     'http'        => ['verify' => __DIR__ . '/cacert.pem'],
// ];

// $s3_client = new S3Client($options);
// $account_id = $_ENV["R2_ACCOUNT_ID"];
// $contents = $s3_client->listObjectsV2([
//     'Bucket' => getBucketName(),
// ]);
// var_dump($contents['Contents']);

// $buckets = $s3_client->listBuckets();
// var_dump($buckets['Buckets']);

function getS3Client(): S3Client
{
    $access_key_id = $_ENV["R2_ACCESS_KEY_ID"];
    $access_key_secret = $_ENV["R2_ACCESS_KEY_SECRET"];

    $credentials = new Aws\Credentials\Credentials($access_key_id, $access_key_secret);

    $options = [
        'region' => 'auto',
        'endpoint' => "https://352fe5bb7c600168419ece2b27337991.r2.cloudflarestorage.com",
        'version' => 'latest',
        'credentials' => $credentials,
        'http'        => ['verify' => __DIR__ . '/cacert.pem'],
    ];

    $s3_client = new S3Client($options);
    return $s3_client;
}

function getBucketName(): string
{
    return $_ENV["R2_BUCKET_NAME"];;
}
