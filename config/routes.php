<?php

class Path {
    public static function __callStatic($method_name, $arguments) {
        $id = null;
        if (count($arguments) > 0) {
            $id = is_numeric($arguments[0])? $arguments[0]: $arguments[0]->id;
        }
        $method_name = strtolower($method_name);
        if ($method_name === "root") {
            return "/";
        }
        if (preg_match("/^add_[a-z_]+$/", $method_name)) {
            $resources = substr($method_name, 4);
            return "/{$resources}/add";
        }
        if (preg_match("/^show_[a-z_]+$/", $method_name)) {
            $resource = substr($method_name, 5);
            if (is_a_resource($resource)) {
                return "/{$resource}";
            } elseif (!empty($id)) {
                $resources = pluralize($resource);
                return "/{$resources}/{$id}";
            }
        }
        if (preg_match("/^edit_[a-z_]+$/", $method_name)) {
            $resource = substr($method_name, 5);
            if (is_a_resource($resource)) {
                return "/{$resource}/edit";
            } elseif (!empty($id)) {
                $resources = pluralize($resource);
                return "/{$resources}/{$id}/edit";
            }
        }
        if (preg_match("/^destroy_[a-z_]+$/", $method_name)) {
            $resource = substr($method_name, 8);
            if (is_a_resource($resource) || empty($id)) {
                return "/{$resource}/destroy";
            } else {
                $resources = pluralize($resource);
                return "/{$resources}/{$id}/destroy";
            }
        }
        return empty($id)? "/{$method_name}": "/{$method_name}/{$id}";
    }
}
