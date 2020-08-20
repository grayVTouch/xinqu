<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVideoTable extends Migration
{
    private $table = 'xq_video';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string('name' , 255)->default('')->comment('名称');
            $table->string('description' , 1000)->default('')->comment('描述');
            $table->unsignedBigInteger('user_id')->default(0)->comment('xq_user.id');
            $table->unsignedBigInteger('module_id')->default(0)->comment('xq_module.id');
            $table->unsignedBigInteger('category_id')->default(0)->comment('xq_category.id');
            $table->string('thumb' , 500)->default('')->comment('用户设置封面');
            $table->string('thumb_for_program' , 500)->default('')->comment('程序智能截取封面');
            $table->unsignedBigInteger('praise_count')->default(0)->comment('点赞数');
            $table->unsignedBigInteger('against_count')->default(0)->comment('反对数');
            $table->unsignedBigInteger('view_count')->default(0)->comment('观看次数');
            $table->unsignedBigInteger('play_count')->default(0)->comment('播放数');
            $table->string('src' , 500)->default('')->comment('视频源');
            $table->tinyInteger('merge_video_subtitle')->default(0)->comment('合并字幕？0-否 1-是');
            $table->unsignedInteger('duration')->default(0)->comment('时长');
            $table->string('type' , 100)->default('')->comment('类别：pro-专题 misc-杂类');
            $table->unsignedBigInteger('video_subject_id')->default(0)->comment('xq_video_subject.id');
            $table->string('simple_preview' , 100)->default('')->comment('简单视频预览（仅有画面无声音的几秒合成预览）');
            $table->string('preview' , 500)->default('')->comment('视频预览图片');
            $table->unsignedInteger('preview_width')->default(0)->comment('视频预览：单个画面尺寸：宽');
            $table->unsignedInteger('preview_height')->default(0)->comment('视频预览：单个画面尺寸：高');
            $table->unsignedInteger('preview_duration')->default(0)->comment('视频预览：单个画面间隔时间');
            $table->unsignedInteger('preview_count')->default(0)->comment('视频预览：合成的画面数量');
            $table->tinyInteger('status')->default(1)->comment('状态：-1-审核不通过 0-审核中 1-审核通过');
            $table->tinyInteger('process_status')->default(0)->comment('处理处理状态：-1-处理失败 0-信息处理中 1-转码中 2-处理完成');
            $table->string('fail_reason' , 1000)->default('')->comment('失败原因，当 status=-1 时，必须提供');
            $table->unsignedSmallInteger('index')->default(0)->comment('剧集索引，仅当 type=pro 的时候有效');
            $table->integer('weight')->default(0)->comment('权重');
            $table->dateTime('update_time')->nullable(true);
            $table->dateTime('create_time')->nullable(true);
        });
        DB::statement("alter table {$this->table} comment '视频表'");
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
