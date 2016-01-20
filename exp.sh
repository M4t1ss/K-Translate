# Usage:
# ./exp.sh languagemodel.binary data.txt

DIR=`dirname $0`
LM=$1
DATA="$2"


echo $DATA | sed -e '$a\' | $DIR/query -v summary $LM | egrep "^(Perplexity excluding OOVs)" | sed -e 's/Perplexity excluding OOVs:	//g'

