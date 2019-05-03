<?php

namespace App\Helpers;

use Illuminate\Http\File;
use Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CustomValidation {

    // Returns true if every character in the string is 2-byte character
    // returns false if the string contains any 1-byte character
    public static function validate2ByteString()
    {
        Validator::extend('two_bytes_string', function($attribute, $value, $parameters, $validator) {
            if (mb_check_encoding($value, 'UTF-8') === false)
                return false;
            $length = mb_strlen($value, 'UTF-8');
            for ($i = 0; $i < $length; $i++) {
                $char = mb_substr($value, $i, 1, 'UTF-8');
                if (mb_check_encoding($char, 'ASCII')) {
                    return false;
                }
            }
            return true;
        });
    }

    // Returns true if every character in the string is 1-byte character from 0x20 to 0x7F ASCII code
    // else return false
    public static function validate1ByteString()
    {
        Validator::extend('one_bytes_string', function($attribute, $value, $parameters, $validator) {
            return !preg_match('/[^\x20-\x7f]/', $value);
        });
    }

   public static function validateNumberAndDashes() {
       Validator::extend('number_dashes', function($attribute, $value, $parameters, $validator) {
            return preg_match('/^[0-9_-]+$/', $value);
       });
   }

    public static function validateNumber() {
        Validator::extend('one_byte_number', function($attribute, $value, $parameters, $validator) {
            return preg_match('/^[0-9]+$/', $value);
        });
    }

    public static function validateLength() {

        Validator::extend('length', function($attribute, $value, $parameters, $validator) {
           return  mb_strlen($value, 'UTF-8')<=$parameters[0];

        });
        Validator::replacer('length', function($message, $attribute, $rule, $parameters) {
            return str_replace(':length', $parameters[0], $message);
        });
    }

    public static function validateCharacterEmail() {
        Validator::extend('email_character', function($attribute, $value, $parameters, $validator) {
            if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $value)) {
                return false;
            }
            $email_array = explode("@", $value);
            $local_array = explode(".", $email_array[0]);
            for ($i = 0; $i < sizeof($local_array); $i++) {
                if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
                    return false;
                }
            }
        });
    }

    public static function validateFormatEmail() {
        Validator::extend('email_format', function($attribute, $value, $parameters, $validator) {
        	return !!filter_var($value, FILTER_VALIDATE_EMAIL);
//            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
//                return false;
//            }
        });
    }

    public static function validateRequiredSelect() {
        Validator::extend('select_required', function($attribute, $value, $parameters, $validator) {
            if (is_null($value)) {
                return false;
            }
            return true;
        });
    }



    public static function validatePhoneNumber() {
        Validator::extend('phone_number', function($attribute, $value, $parameters, $validator) {
            return preg_match("/^([0-9\s\-]*)$/", $value);
        });
    }
    public static function validateFaxNumber() {
        Validator::extend('fax_number', function($attribute, $value, $parameters, $validator) {
            return preg_match("/^([0-9\s\-]*)$/", $value);
        });
    }
    public static function validateZipCode() {
        Validator::extend('zip_code', function($attribute, $value, $parameters, $validator) {
            return preg_match("/^[0-9]+$/", $value);
        });
    }

    public static function validateKana() {
        Validator::extend('kana', function($attribute, $value, $parameters, $validator) {
            if (preg_match("/^[ァ-ヶー]+$/u", $value)) {
                return true;
            }
            return false;
        });
    }
    public static function validateKanaCustom() {
        Validator::extend('kana_custom', function($attribute, $value, $parameters, $validator) {
                if(empty($value))
                {
                    return true;
                }
                if (preg_match("/^[ぁ-んァ-ヶーｦ-ﾟ-]+$/u",$value)) {
                    return true;
                }
               return false;
        });
    }

    public static function validateBetween() {
        Validator::extend('between_custom', function($attribute, $value, $parameters, $validator) {
            $validator->addReplacer('between_custom', function($message, $attribute, $rule, $parameters){
                return str_replace([':min',':max'], $parameters, $message);
            });
            if($value < $parameters[0] || $value > $parameters[1]){
                return false;
            }
            return true;
        });
    }

    public static function validateDateFormat(){
        Validator::extend('date_format_custom', function($attribute, $value, $parameters, $validator) {

            if (preg_match("/^[0-9]{4}(0[1-9]|1[0-2])$/",$value)) {
                return true;
            } else {
                return false;
            }
        });
    }

    public static function validateFileSize(){
        Validator::extend('max_mb', function($attribute, $value, $parameters, $validator) {

            if ($value instanceof UploadedFile && ! $value->isValid()) {
                return false;
            }

            $megabytes = $value->getSize() / 1024 / 1024;

            return $megabytes <= $parameters[0];
        });
        Validator::replacer('max_mb', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':max' , $parameters[0], $message);
        });
    }

    public static function validateDecimal(){
        Validator::extend('decimal_custom', function($attribute, $value, $parameters, $validator) {
            return preg_match("/^([0-9\s\.]*)$/", $value);
        });
    }

    public static function validate_base64($base64data, array $allowedMime)
    {
        // strip out data uri scheme information (see RFC 2397)
        if (strpos($base64data, ';base64') !== false) {
            list(, $base64data) = explode(';', $base64data);
            list(, $base64data) = explode(',', $base64data);
        }

        // strict mode filters for non-base64 alphabet characters
        if (base64_decode($base64data, true) === false) {
            return false;
        }

        // decoding and then reeconding should not change the data
        if (base64_encode(base64_decode($base64data)) !== $base64data) {
            return false;
        }

        $binaryData = base64_decode($base64data);

        // temporarily store the decoded data on the filesystem to be able to pass it to the fileAdder
        $tmpFile = tempnam(sys_get_temp_dir(), 'medialibrary');
        file_put_contents($tmpFile, $binaryData);

        // guard Against Invalid MimeType
        $allowedMime = array_flatten($allowedMime);

        // no allowedMimeTypes, then any type would be ok
        if (empty($allowedMime)) {
            return true;
        }

        // Check the MimeTypes
        $validation = Validator::make(
            ['file' => new File($tmpFile)],
            ['file' => 'mimes:' . implode(',', $allowedMime)]
        );

        return !$validation->fails();
    }

    public static function validateImage(){
        Validator::extend('is_image',function($attribute, $value, $params, $validator) {
            return self::validate_base64($value, ['png', 'jpg', 'jpeg']);
        });
    }

    public static function validateNumberRange(){
        Validator::extend('number_range', function($attribute, $value, $parameters, $validator) {
            if ($value > 2147483647) {
                return false;
            } else {
                return true;
            }
        });
    }
}