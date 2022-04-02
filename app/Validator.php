<?php
namespace App;

class Validator
{
    /**
     * Очищает получаемые данные от лишних пробелов, преобразует html сущности
     *
     * @param $field string
     * @return string
     */
    public static function clean($field){
        return trim(htmlspecialchars($field));
    }

    /**
     * Проверяем соотвествует ли переданные данные требованиям
     *
     * @param $fields array
     * @return array
     */
    public static function validate($fields){
        $errors = [];

        foreach ($fields as $field) {

            $rules = explode("|", $field['rules']);

            foreach ($rules as $rule) {
                switch ($rule) {
                    case 'required':
                        if(empty($field['data'])) {
                            $errors['required'] = "Не заполнено обязательное поле  {$field['name']} ";
                        }
                        break;
                    case 'numeric':
                        if(!is_numeric($field['data'])) {
                            $errors['numeric'] = "Поле  {$field['name']} не является числом";
                        }
                        break;
                    case 'email':
                        if(!filter_var($field['data'], FILTER_VALIDATE_EMAIL)) {
                            $errors['email'] = "Поле {$field['name']} не является эмейлом";
                        }
                        break;
                }
            }
        }

        if(count($errors) > 0) {
            return $errors;
        }
    }
}