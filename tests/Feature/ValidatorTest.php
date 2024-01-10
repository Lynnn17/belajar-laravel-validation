<?php

namespace Tests\Feature;

use App\Rules\Uppercase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

class ValidatorTest extends TestCase
{
    public function testValidator()
    {
        $data = [
            "username" => "admin",
            "password" => "12345"
        ];

        $rules = [
            "username" => "required",
            "password" => "required"
        ];

        $validator  = Validator::make($data,$rules);
        self::assertNotNull($validator);
        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());
    }

    public function testValidatorInvalid()
    {
        $data = [
            "username" => "",
            "password" => ""
        ];

        $rules = [
            "username" => "required",
            "password" => "required"
        ];

        $validator  = Validator::make($data,$rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        //untuk mendapatkan massage ketika error
        $message = $validator->getMessageBag();

        // bisa menggunakan cara dibawah
        // $message->get("username");
        // $message->get("password");

        // atau bisa cara ini untuk mendapatkan semua dan menjadikan json
        Log::info($message->toJson(JSON_PRETTY_PRINT));



    }

    public function testValidatorValidationException()
    {
        $data = [
            "username" => "",
            "password" => ""
        ];

        $rules = [
            "username" => "required",
            "password" => "required"
        ];

        $validator  = Validator::make($data,$rules);
        self::assertNotNull($validator);

        try {
            $validator->validate();
            self::fail("ValidationException not thrown");
        }catch (ValidationException $exception){
            assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }


    }

    public function testValidatorMultipleRules()
    {
        $data = [
            "username" => "LYN",
            "password" => "LYN"
        ];

        $rules = [
            //bisa dengan 2 cara ya itu | atau dengan array
            "username" => "required|email|max:100",
            "password" => ["required","min:6","max:20"]
        ];

        $validator  = Validator::make($data,$rules);
        self::assertNotNull($validator);

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));


    }


    public function testValidatorValidData()
    {
        // untuk Validation Message menggunakan folder lang/sesuai dengan setLocal nya
        App::setLocale('id');
        $data = [
            "username" => "admin@lyn.com",
            "password" => "12345",
            "admin" => true
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => ["required","min:6","max:20"]
        ];

        $validator  = Validator::make($data,$rules);
        self::assertNotNull($validator);

        try {
            //mengambil data yang hanya valid/sudah di validasi
            $valid = $validator->validate();
            Log::info(json_encode($valid, JSON_PRETTY_PRINT));
        }catch (ValidationException $exception){
            assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }


    }

    public function testValidatorInlineMassage()
    {
        $data = [
            "username" => "adminlyn.com",
            "password" => "12345",
            "admin" => true
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => ["required","min:6","max:20"]
        ];
        // tidak di rekomendasikan seperti ini, lebih baik mengganti di folder lang
        $massages = [
            "required" => ":attribute harus diisi",
            "email" => ":attribute harus berupa email",
            "min" => ":attribute minimal :min karakter",
            "max" => ":attribute maksimal :max karakter",
        ];

        $validator  = Validator::make($data,$rules,$massages);
        self::assertNotNull($validator);

       assertFalse($validator->passes());
       assertTrue($validator->fails());

       $message = $validator->getMessageBag();

       Log::info($message->toJson(JSON_PRETTY_PRINT));

    }

    public function testValidatorAdditionalValidator()
    {
        $data = [
            "username" => "lyn@gmail.com",
            "password" => "lyn@gmail.com"
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => ["required","min:6","max:20"]
        ];

        $validator  = Validator::make($data,$rules);
        //coba menambahkan aturan baru
        $validator->after(function (\Illuminate\Validation\Validator $validator){
            $data = $validator->getData();
            if ($data['username'] == $data['password']){
                $validator->errors()->add("password","Password tidak boleh sama dengna username");
            }
        });
        self::assertNotNull($validator);

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));


    }

    //cara menambahkan custom rule
    public function testValidatorCustomRule()
    {
        $data = [
            "username" => "lyn@gmail.com",
            "password" => "lyn@gmail.com"
        ];

        $rules = [
            "username" => ["required","email","max:100", new Uppercase()],
            "password" => ["required","min:6","max:20"]
        ];

        $validator  = Validator::make($data,$rules);
        self::assertNotNull($validator);

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));


    }


}
