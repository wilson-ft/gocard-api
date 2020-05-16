## GoCard API Services
API services for GoCard application. The purpose of this API is to provide mobile application authentication using AWS Rekognition to detect and compare customer face and also to manage customer Mambu balance account.

### About
This API service is built using the latest Laravel Framework 7.0 and it integrates multiple third party services such as:
- Mambu
> Enroll and manage customer saving accounts.
- AWS S3
> Store persistent data.
- AWS Rekognition
> Detect and compare customer faces.

### Prerequisites
- PHP 7.2
- Composer

### Environment Setup
    FILESYSTEM_CLOUD="s3"
    FILESYSTEM_DRIVER="s3"
    AWS_SECRET_ACCESS_KEY=<Your AWS Secret Access Key>
    AWS_ACCESS_KEY_ID=<Your AWS Key Id>
    AWS_DEFAULT_REGION=<Your AWS Region>
    AWS_BUCKET=<Your AWS Bucket Name>
    MAMBU_URL=<Mambu Base URL>
    MAMBU_USERNAME=<Mambu Username>
    MAMBU_PASSWORD=<Mambu Password>
    MAMBU_BRANCH_KEY=<Mambu Branch Key>
    MAMBU_MAIN_SAVING_ACCOUNTS_ID=<Default Mambu Saving Accounts Id>

### How to Run  
- Clone this repository to your machine.
- Install dependencies with `composer install` command.
- Set up your environment variable with .env file.
- Migrate the database with `php artisan migrate` & `php artisan db:seed` command.
- Run the server with `php artisan serve`. (The default url & port would be http://localhost:8000).
