# cookie操作
> 封装cookie操作,会对设置的COOKIE进行加密操作,有效防止篡改
> 接口参考kohana的cookie类


快捷安装:
```
composer require lsys/cookie
```

使用示例:
```
//设置$salt,用来加密cookie用,一般初始化时设置
//如果不设置$salt,COOKIE 加密不被启用
Cookie::$salt='your salt';
//设置COOKIE
Cookie::set("tt","bbcccc");
//得到cookie
echo Cookie::get("tt");
```