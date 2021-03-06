<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEmailCodeTable extends Migration
{
    private $table = 'xq_email_code';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string('email' , 30)->default('')->comment('邮箱');
            $table->string('code' , 30)->default('')->comment('验证码');
            $table->string('type' , 30)->default('')->comment('类型，比如：login-登录验证码 register-注册验证码 password-修改密码验证码 等');
            $table->tinyInteger('is_used')->default(0)->comment('是否被使用过？0-否 1-是');
            $table->datetime('send_at')->nullable(true)->comment('发送时间');

            $table->timestamps();

            $table->unique(['email' , 'type']);

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        DB::statement("alter table {$this->table} comment '邮箱验证码'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
