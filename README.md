# File Converter

Simple file converter to convert data between different formats while saving data structure.

Currently supports converting json to cvs and vise versa.

Run an application and open homepage to use converter.

Will show a form with two fields: File upload and result file mime type.

After uploading csv or json file and submitting form it will respond with result file download.

## Architecture
* First converter will try to find a service implementing FileConverterInterface that supports uploaded file's mime type.
* If found parser will parse file to create data array of \stdClass objects and all nested arrays.
* Then converter tries to find a service implementing FileCreatorInterface that supports selected output mime type.
* If found creator will create BinaryFileResponse from data array
* Controller will return BinaryFileResponse from file creator.
* If any error occurred during this process it will be displayed as danger status message and form will be reloaded.

## Dependencies
 * [Docker](https://www.docker.com/get-started)

## Start the server

```
docker-compose up -d
```
Access your website using [http://localhost:8000](http://localhost:8000)

## Destroy your docker container

```
docker-compose down
```
