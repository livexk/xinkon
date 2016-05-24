<?php


namespace library;

class  controller extends view
{
    protected function ajaxReturn($data, $status = 1)
    {
        $tem = ['info' => $data, 'status' => $status];
        header('Content-Type: data/json');
        exit(json_encode($tem));
    }
}