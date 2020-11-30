# Sparql Toucan - a typo3 Extension

some explanation what i am even doing here later





## Cpt. Logs

*30.11.2020* 

In order to achieve the functionality to get language specific LanguagePoints in the Frontend i leveraged the deprecated feature of *CachedValue* for Datapoints. The Value in the database does nothing on its own but while rendering the front end i am manipulating the content of that field to the specific displayed language (or the default if none is found). This feels quite dirty but works exactly as expected in a neat way that is easily accessible. I am still second guessing my steps here.

