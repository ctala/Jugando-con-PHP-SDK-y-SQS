<?php

/*
 * The MIT License
 *
 * Copyright 2017 Cristian Tala <yomismo@cristiantala.cl>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require 'vendor/autoload.php';
include_once 'sharedConfig.php';

// Creamos la clase SDK.
$sdk = new Aws\Sdk($sharedConfig);

// Creamos el cliente SQS desde el SDK
$client = $sdk->createSqs();

// Obtenemos la URL de la queue.
$result = $client->getQueueUrl(array('QueueName' => $my_queue_name));
$queue_url = $result->get('QueueUrl');

/*
 * Si no hay un mensaje en el Queue 
 * esperaremos un tiempo
 * definido por backoff y delimitado por 
 * $backoffMax
 */

$backoff = 0;
$backoffMax = 3;
while (true) {

    try {
        $message = $sqs_client->receiveMessage(array(
            'QueueUrl' => $queue_url
        ));
    } catch (Exception $exc) {
        echo "No se pudo obtener mensaje \n";
        echo $exc->getTraceAsString();
    }

    if ($message['Messages'] == null) {
        // No hay mensajes a procesar.
        echo "No hay mensajes a procesar. Duermo.\n";
        $backoff += 0.5;
        if ($backoff > $backoffMax) {
            $backoff = $backoffMax;
        }
        echo "Espero $backoff segundos";
        sleep($backoff);
    } else {
        $backoff = 0;
        echo "Hay mensajes a procesar. Proceso.\n";
        // Obtengo la información del mensaje

        $result_message = array_pop($message['Messages']);
        $queue_handle = $result_message['ReceiptHandle'];
        $message_json = $result_message['Body'];

        //Imprimimos el contenido del mensaje
        print_r($message_json);

        echo "\n";
        //Ahora eliminamos.

        try {
            $sqs_client->deleteMessage(array(
                'QueueUrl' => $queue_url,
                'ReceiptHandle' => $queue_handle
            ));
            echo "\t Mensaje eliminado\n";
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
            echo "\t Mensaje NO eliminado\n";
        }
    }
}






 