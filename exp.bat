echo %~1 > inputText.txt
java -Xmx1024m -jar BerkeleyParser\BerkeleyParser-1.7.jar -gr BerkeleyParser\eng_sm6.gr < inputText.txt
del inputText.txt