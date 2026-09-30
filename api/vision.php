<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Este archivo es el encargado de procesar las peticiones de la API de Vision de Google Cloud y devolver los resultados
// lo que hace es extraer el texto de un archivo PDF y devolverlo en un string de texto plano
// con la librería de Google Cloud Vision para PHP 
// "google/apiclient": "^2.12",
// "google/cloud-vision": "^1.5"

// Incluye la biblioteca de Google Cloud client library
require_once '../vendor/autoload.php';

// Incluye la biblioteca de Google Cloud client library
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

// Establecemos la ruta al archivo de clave JSON de autenticación
putenv('GOOGLE_APPLICATION_CREDENTIALS=../key.json');

// Creamos una nueva instancia de la API de Vision
$client = new ImageAnnotatorClient();

// Cargamos el archivo PDF en una variable
$pdf_content = file_get_contents('32D.jpg');

// Creamos una instancia de la clase Image con el contenido del archivo PDF
$image = (new \Google\Cloud\Vision\V1\Image())
    ->setContent($pdf_content);

// Llamamos a la función documentTextDetection de la API de Vision para extraer el texto completo del PDF
$response = $client->documentTextDetection($image);
$document = $response->getFullTextAnnotation();

// Recorremos todos los bloques de texto del documento y construimos una cadena de texto con el contenido de cada bloque
$text = '';
foreach ($document->getPages() as $page) {
    foreach ($page->getBlocks() as $block) {
        $blockText = '';
        foreach ($block->getParagraphs() as $paragraph) {
            foreach ($paragraph->getWords() as $word) {
                foreach ($word->getSymbols() as $symbol) {
                    $blockText .= $symbol->getText();
                }
                $blockText .= ' ';
            }
            $blockText .= ' ';
        }
        $text .= $blockText;
    }
}

// Devolvemos el texto extraído del PDF
echo $text;


// Cerramos la sesión de la API de Vision
$client->close();



// // Incluye la biblioteca de Google Cloud client library
// require_once '../vendor/autoload.php';

// // Incluye la biblioteca de Google Cloud client library
// use Google\Cloud\Vision\V1\ImageAnnotatorClient;

// // Establecemos la ruta al archivo de clave JSON de autenticación
// putenv('GOOGLE_APPLICATION_CREDENTIALS=../key.json');

// try {
//     // Convertimos el archivo PDF a una imagen en formato JPEG
//     exec("pdftoppm -jpeg Documento-693-12054.pdf image");

//     // Creamos una nueva instancia de la API de Vision
//     $client = new ImageAnnotatorClient();

//     // Cargamos el archivo PDF en una variable
//     $pdf_content = file_get_contents('image.jpg');

//     // Creamos una instancia de la clase Image con el contenido del archivo PDF
//     $image = (new \Google\Cloud\Vision\V1\Image())
//         ->setContent($pdf_content);

//     // Llamamos a la función documentTextDetection de la API de Vision para extraer el texto completo del PDF
//     $response = $client->documentTextDetection($image);
//     $document = $response->getFullTextAnnotation();

//     // Recorremos todos los bloques de texto del documento y construimos una cadena de texto con el contenido de cada bloque
//     $text = '';
//     foreach ($document->getPages() as $page) {
//         foreach ($page->getBlocks() as $block) {
//             $blockText = '';
//             foreach ($block->getParagraphs() as $paragraph) {
//                 foreach ($paragraph->getWords() as $word) {
//                     foreach ($word->getSymbols() as $symbol) {
//                         $blockText .= $symbol->getText();
//                     }
//                     $blockText .= ' ';
//                 }
//                 $blockText .= ' ';
//             }
//             $text .= $blockText;
//         }
//     }

//     // Devolvemos el texto extraído del PDF
//     echo $text;

//     // Cerramos la sesión de la API de Vision
//     $client->close();
// } catch (Exception $e) {
//     // Si ocurre algún error, imprimimos el mensaje de error
//     echo "Error: " . $e->getMessage();
// }