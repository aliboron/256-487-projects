<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/env.php';

use Aws\S3\S3Client;

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
