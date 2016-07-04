echo %~1 > input.txt
KenLM\ngram_query.exe KenLM\JRC-lv.arpa < input.txt
del input.txt