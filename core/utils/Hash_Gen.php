<?php
namespace utils;

class Hash_Gen
{
    private $PEPPER;
    private $SECRET;

    public function __construct()
    {
        $env = new \utils\Env_Bootstrap();

        $this->PEPPER = $env->get_var("PEPPER");
        $this->SECRET = $env->get_var("SECRET");
    }

    public function salt($size = 16)
    {
        return bin2hex(random_bytes($size));
    }

    public function hash($password, $salt)
    {
        return hash("sha256", $password . $salt . $this->PEPPER);
    }

    public function hmac_hash($data, $seed, $token=false) {
        $serial = json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        if($token) {
            return hash_hmac("sha256", $serial . $seed, $token);
        }

        return hash_hmac("sha256", $serial . $seed, $this->SECRET);
    }

    public function get_pepper() {
        return $this->PEPPER;
    }
}
