php 编码的问题:

php的输出编码跟:
------------------------------------------------------------
header("Content-Type: text/html; charset=UTF-8");

和

php文件本身的保存编码有关系
------------------------------------------------------------


1 客户端向服务端发送的内容的字符集编码:

如果使用客户端的php编码是UTF-8，则需要设置如下，参考 demo.php 第 58 行
$client->setOutgoingEncoding("UTF-8");

2 客户端接收到的字符集编码转换
由于demo.php采用的是UTF-8编码，从服务端返回的也是UTF-8编码，则可直接输出，没有乱码
假如是使用GBK编码的，如 demo_gbk.php ,除了需要设置
$client->setOutgoingEncoding("GBK"); 
之外，显示出的内容也需要进行 UTF-8 => GBK 的转换，如 295 行


