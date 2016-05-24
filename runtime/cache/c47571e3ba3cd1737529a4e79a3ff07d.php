<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $dd; ?></title>
    <link  href="<?php echo $STATIC; ?>/css/base.css" rel="stylesheet" media="screen"/>
</head>
<body>

<header>
<h1>在线http测试工具</h1>
</header>
<div class="main">
    <div class="choose">
        <b>方式以及链接: 链接: http:// 开头
            <button onclick="clean();"> 清空结果</button>
            <button class="status"> 查询状态</button>
        </b>
        <select name="type" id="input_type">
            <option value="post">POST</option>
            <option value="get">GET</option>
            <option value="empty">EMPTY</option>
        </select>
        <input type="text" id="url">
        <button type="button" onclick="update(this);">提交</button>
    </div>
    <div class="value">
        <b>参数: 类似( aa=bb&cc=dd&ee=ff ) </b>
        <textarea name="value" id="parameter"></textarea>
    </div>
    <div class="content">
        <pre id="header">

        </pre>
        <pre id="body">

        </pre>
    </div>
    <div class="right">
        <div>
            <b>历史记录:</b>
            <button onclick="clean_cache();">清除记录</button>
            <button onclick="close_cache();">收起记录</button>
        </div>
        <div class="body">

        </div>
    </div>
    <div class="right_button" onclick="right_button(this);">
        <div class="san"></div>
        <b>展</b>
        <b>开</b>
        <b>历</b>
        <b>史</b>
    </div>
</div>
<script src="<?php echo $COMMON; ?>/lib/jquery/jquery.min.js"></script>
<script src="<?php echo $STATIC; ?>/js/app.js"></script>
<footer>
</footer>
</body>
</html>
