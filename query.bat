echo %~1 > input.txt
KenLM\ngram_query.exe %~2 < input.txt
del input.txt