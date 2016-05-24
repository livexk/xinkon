function update(o) {
    var $type = document.getElementById('input_type').value;
    var $url = document.getElementById('url').value;
    var $parameter = document.getElementById('parameter').value;

    if ($url.match('[a-zA-z]+://[^\s]*') == null) {
        document.getElementById('url').setAttribute('class', 'error');
        return false;
    } else {
        document.getElementById('url').setAttribute('class', ' ');
    }
    if ($type !== 'empty') {
        if ($parameter.match('[a-z0-9]*=[^&]+') == null) {
            document.getElementById('parameter').setAttribute('class', 'error');
            return false;
        }
    }
    cache_handle($url, $parameter, $type);
    document.getElementById('parameter').setAttribute('class', ' ');
    $(o).attr('disabled', 'disabled');
    $('.status').text("查询中....").addClass('error');
    $.get('index.php?r=web/index/handle', {url: $url, type: $type, parameter: $parameter}, function (data) {
        $(o).removeAttr('disabled');
        $('.status').text("查询成功!").addClass('error');
        if (data.status == 200) {
            $('#body').html(syntaxHighlight(data.body));
            $('#header').html(data.header);
        } else {
            $('#body').html(data.body);
            $('#header').html(data.header);
        }
    });

}


function syntaxHighlight(json) {
    if (typeof json != 'string') {
        json = JSON.stringify(json, undefined, 2);
    }
    json = json.replace(/&/g, '&').replace(/</g, '<').replace(/>/g, '>');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}

function clean() {
    $('#header').html("");
    $('#body').html("");
    $('.status').text("查询状态").removeClass('error');
}
var store;
var objectStore;
const dbname = 'xinkon';
cache_handle();

function cache_handle($url, $parameter, $type) {
    if (!window.indexedDB) {
        window.alert("浏览器不支持");
        return false;
    }
    var db;
    var request = window.indexedDB.open(dbname, 3);

    request.onerror = function (event) {
        //console.log("Database error: " + event.target.errorCode);
    };
    request.onsuccess = function (event) {
        db = event.target.result;
        transaction = db.transaction("cache", 'readwrite');
        objectStore = transaction.objectStore("cache");
        if ($url == undefined || $url == '') {
            detail_data(objectStore);
        } else {
            get_data_index(objectStore);
        }
    };
    request.onupgradeneeded = function (event) {
        db = event.target.result;
        store = db.createObjectStore("cache", {autoIncrement: true});
        store.createIndex("type", "type");
        store.createIndex("url", "url");
        store.createIndex("parameter", "parameter");
    };

    /**
     * 负责查数据
     * @param db
     */
    detail_data = function (objectStore) {
        var request = objectStore.openCursor();//openCursor没有参数的时候，表示获得所有数据
        var sub = new Array;
        request.onsuccess = function (e) {//openCursor成功的时候回调该方法
            var cursor = e.target.result;
            if (cursor) {//循环遍历cursor
                sub.push(cursor.value);
                cursor.continue();
            } else {
                findAll(sub);
            }
        }
    };

    /**
     * 添加数据
     */
    add_data = function (objectStore) {
        var result = objectStore.put({type: $type, url: $url, parameter: $parameter});
        detail_data(objectStore);
        return true;
    };

    get_data_index = function (objectStore) {
        var request = objectStore.openCursor();//openCursor没有参数的时候，表示获得所有数据
        var status = 1;
        request.onsuccess = function (e) {//openCursor成功的时候回调该方法
            var cursor = e.target.result;
            if (cursor) {//循环遍历cursor
                var cache = cursor.value;
                if (cache.type == $type && cache.url == $url && cache.parameter == $parameter) {
                    status = 0;
                }
                cursor.continue();
            } else {
                console.log(status);
                if (status == 1) {
                    add_data(objectStore);
                }
            }
        };
    }

}
function findAll($data) {
    var str = "";
    var k = 0;
    for (var i = 0; i < $data.length; i++) {
        k++;
        var tem = $data[i];
        str += '<a href="javascript:;" title="' + tem['parameter'] + '请求类型:' + tem['type'] + ' "  data-url="' + tem['url'] + '"  data-type="' + tem['type'] + '"   data-parameter="' + tem['parameter'] +
            '" onclick="chang_url(this);">' + k + ' : &nbsp;{&nbsp;链接:&nbsp;}' + tem['url'] + '<br>&nbsp;{&nbsp;参数:&nbsp;}' + tem['parameter'] + '<br>&nbsp;{&nbsp;类型:&nbsp;}' + tem['type'] + '</a><hr>';
    }
    $('.body').html(str);
}

function chang_url(o) {
    var url = $(o).attr('data-url');
    var type = $(o).attr('data-type');
    var parameter = $(o).attr('data-parameter');
    $('#url').val(url);
    $('#parameter').val(parameter);
    $('#input_type').val(type);
}

function clean_cache() {
    var request = window.indexedDB.open(dbname, 3);
    request.onsuccess = function (event) {
        db = event.target.result;
        transaction = db.transaction("cache", 'readwrite');
        objectStore = transaction.objectStore("cache");
        objectStore.clear();
        detail_data(objectStore);
    };
}

function right_button(o) {
    $(o).hide();
    $('.right').show();
}
function close_cache(o) {
    $('.right').hide();
    $('.right_button').show();
}