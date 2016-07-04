echo %~1 > inputText.txt
java -Xmx1024m -jar BerkeleyParser\BerkeleyParser-1.7.jar -gr %~2 < inputText.txt
del inputText.txt