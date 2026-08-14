<?php
namespace utils;

class Response_Handler {
    public function send($data, $tag, $token, $api_status = true) {
        $hash_gen = new \utils\Hash_Gen();
        $seed = $hash_gen->salt(32);
        $auth = $hash_gen->hmac_hash($data, $seed, $token);


        $response = [
            "success" => $api_status,
            "request_tag" => $tag,
            "seed" => $seed,
            "data" => $data,
            "auth" => $auth
        ];

        echo json_encode($response);
    }
}
