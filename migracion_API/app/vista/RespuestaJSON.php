<?php

/**
 * @brief Gestiona las respuestas JSON enviadas por la API.
 *
 * Permite devolver respuestas exitosas o mensajes de error
 * utilizando el formato JSON.
 */
class RespuestaJson
{
    /**
     * @brief Envía una respuesta exitosa en formato JSON.
     *
     * @param mixed $datos Datos que serán enviados al cliente.
     * @param int $status Código de estado HTTP.
     *
     * @return void
     */
    public static function exito($datos, int $status = 200): void
    {
        http_response_code($status);
        header("Content-Type: application/json");
        echo json_encode(["datos" => $datos]);
        exit;
    }

    /**
     * @brief Envía una respuesta de error en formato JSON.
     *
     * @param string $mensaje Mensaje de error.
     * @param int $status Código de estado HTTP.
     *
     * @return void
     */
    public static function error(string $mensaje, int $status): void
    {
        http_response_code($status);
        header("Content-Type: application/json");
        echo json_encode(["mensaje" => $mensaje]);
        exit;
    }
}