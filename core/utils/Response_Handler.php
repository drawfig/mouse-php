<?php
namespace utils;

class Response_Handler {
    public function send($data, $action, $tag, $token, $api_status = true) {
        $hash_gen = new \utils\Hash_Gen();
        $seed = $hash_gen->salt(32);
        $timestamp = (int) round(microtime(true) * 1000);
        $envelope = $action . $tag . $seed . $timestamp;

        $auth = $hash_gen->hmac_hash($data, $envelope, $token);


        $response = [
            "success" => $api_status,
            "action" => $action,
            "request_tag" => $tag,
            "seed" => $seed,
            "timestamp" => $timestamp,
            "data" => $data,
            "auth" => $auth
        ];

        echo json_encode($response);
    }
}
