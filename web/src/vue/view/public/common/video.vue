<template>
    <div class="video-player">
        <div class="title hover">
            <div class="name"></div>
            <div class="other"></div>
        </div>
        <div class="video-wrapper">
            <video class="video" crossorigin="anonymous"></video>
        </div>
        <div class="control">
            <div class="tip"></div>
            <div class="progress">
                <div class="inner">
                    <div class="amount">
                        <div class="ratio"></div>
                        <div class="loaded"></div>
                        <div class="at-position"></div>
                    </div>
                    <div class="point">
                        <div class="c-point"></div>
                    </div>
                </div>
            </div>
            <div class="actions">
                <div class="left">
                    <div class="play-ctrl play run-action-feedback-round"><i class="run-iconfont run-iconfont-danseshixintubiao-"></i></div>
                    <div class="play-ctrl pause hide run-action-feedback-round"><i class="run-iconfont run-iconfont-zanting1"></i></div>
                    <div class="video-ctrl prev run-action-feedback-round"><i class="run-iconfont run-iconfont-ai10"></i></div>
                    <div class="video-ctrl next run-action-feedback-round"><i class="run-iconfont run-iconfont-ai09"></i></div>
                    <div class="sound-ctrl">
                        <div class="muted-ctrl">
                            <div class="not-muted run-action-feedback-round"><i class="run-iconfont run-iconfont-shengyin"></i></div>
                            <div class="muted hide run-action-feedback-round"><i class="run-iconfont run-iconfont-jingyin"></i></div>
                        </div>
                        <div class="sound">
                            <div class="inner">
                                <div class="ratio"></div>
                                <div class="point"></div>
                            </div>
                        </div>
                    </div>
                    <div class="time-info">
                        <span class="current-time">--:--</span>
                        /
                        <span class="duration">--:--</span>
                    </div>
                </div>
                <div class="right">
                    <div class="definition">
                        <span class="name"></span>
                        <div class="settings hide"></div>
                    </div>
                    <div class="speed">
                        <span class="name"></span>
                        <div class="settings hide"></div>
                    </div>
                    <div class="transform run-action-feedback-round">
                        <i class="run-iconfont run-iconfont-shunshizhenxuanzhuan"></i>
                    </div>
                    <div class="settings">
                        <div class="set run-action-feedback-round"><i class=" run-iconfont run-iconfont-shezhi"></i></div>
                        <div class="config hide">
                            <div class="operation">
                                <div class="item subtitle" data-id="subtitle">
                                    <div class="name">字幕</div>
                                    <div class="value">
                                        <span class="text"></span><i class="run-iconfont run-iconfont-next01"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mapping">
                                <div class="mapping-item subtitle hide" data-id="subtitle">
                                    <div class="title">
                                        <div class="name back-to-operation">
                                            <div class="text"><i class="run-iconfont run-iconfont-prev01 v-a-m m-r-5"></i>切换字幕</div>
                                            <div class="action"></div>
                                        </div>
                                        <div class="action"></div>
                                    </div>
                                    <div class="list"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="screen not-full-screen run-action-feedback-round"><i class="run-iconfont run-iconfont-quanping"></i></div>
                    <div class="screen full-screen run-action-feedback-round"><i class="run-iconfont run-iconfont-quanping1"></i></div>
                </div>
            </div>
        </div>
        <div class="preview hide">
            <div class="inner">
                <div class="move">
                    <div class="image-wrapper">
                        <img src="" alt="" class="image">
                    </div>
                    <div class="time-point">--:--</div>
                </div>
            </div>
        </div>
        <div class="loading hide">
            <i class="run-iconfont run-iconfont-jiazai_dan"></i>
        </div>
        <div class="poster">
            <img src="" alt="" class="image">
        </div>
        <div class="play-ctrl-tip hide">
            <div class="item play hide"><i class="run-iconfont run-iconfont-danseshixintubiao-"></i></div>
            <div class="item pause hide"><i class="run-iconfont run-iconfont-zanting1"></i></div>
            <div class="item volume hide"><i class="run-iconfont run-iconfont-shengyin"></i></div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "my-video"
    }
</script>

<style scoped>

</style>
