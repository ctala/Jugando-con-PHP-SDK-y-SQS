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

// Creamos la QUEUE
$queue_options = array(
    'QueueName' => $my_queue_name
);

try {
    $client->createQueue($queue_options);
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
    die('Error creando la queue ' . $exc->getMessage());
}


// Obtenemos la URL de la queue.
$result = $client->getQueueUrl(array('QueueName' => $my_queue_name));
$queue_url = $result->get('QueueUrl');

print_r($queue_url);

// The message we will be sending
$our_message = array(
    'tipo' => 'MAIL',
    'content' => rand(0, 110000010)
);

// Send the message

try {
    $client->sendMessage(array(
        'QueueUrl' => $queue_url,
        'MessageBody' => json_encode($our_message)
    ));
} catch (Exception $ex) {
    die('Error enviando el mensaje a la queue ' . $e->getMessage());
}
