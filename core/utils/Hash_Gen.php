<?php
namespace utils;

class Hash_Gen
{
    private $PEPPER;

    public function __construct()
    {
        $env = new \utils\Env_Bootstrap();

        $this->PEPPER = $env->get_var("PEPPER");
    }

    public function salt($size = 16)
    {
        return bin2hex(random_bytes($size));
    }

    public function hash($password, $salt)
    {
        return hash("sha256", $password . $salt . $this->PEPPER);
    }

    public function get_pepper() {
        return $this->PEPPER;
    }
}
