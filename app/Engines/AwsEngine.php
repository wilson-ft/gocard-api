<?php

namespace App\Engines;

use Aws\Rekognition\RekognitionClient;

use DB;
use Log;

use Illuminate\Support\Facades\Storage;

class AwsEngine
{
    public function __construct()
    {

    }

    public function comparePhotos($source, $target)
    {
        $client = new RekognitionClient([
                    'region'    => env('AWS_DEFAULT_REGION'),
                    'version'   => 'latest'
                ]);

        try {
            $result = $client->compareFaces([
                'QualityFilter'         => 'AUTO',
                'SimilarityThreshold'   => 80,
                'SourceImage' => [
                    'S3Object' => [
                        'Bucket'    => env('AWS_BUCKET'),
                        'Name'      => $source
                    ],
                ],
                'TargetImage' => [
                    'S3Object' => [
                        'Bucket'    => env('AWS_BUCKET'),
                        'Name'      => $target
                    ],
                ],
            ]);

            if($result['FaceMatches'][0]["Similarity"] < 80){
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }

        return true;
    }
}
