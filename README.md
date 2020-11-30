# Sparql Toucan - a Typo3 Extension

This Plugin aims to include data from a Linked Data Source into a Website.

It contains two parts, the module and the plugin. One includes the data in the front end in the least intrusive way possible to allow the usual customization options of typo3 while doing all steps necessarily to stay true.

On the back end site there are several functions to make the use easier. A wizard to easily explore a sparql endpoint and some assistance functions to generate views for a specific set of data.





## Cpt. Logs

*30.11.2020* - 11:25

In order to achieve the functionality to get language specific LanguagePoints in the Frontend i leveraged the deprecated feature of *CachedValue* for Datapoints. The Value in the database does nothing on its own but while rendering the front end i am manipulating the content of that field to the specific displayed language (or the default if none is found). This feels quite dirty but works exactly as expected in a neat way that is easily accessible. I am still second guessing my steps here.

*30.11.2020 - 14:10*

I have additional doubts regarding the way i am doing CollectionEntry Styles. I am currently using a set of *CSS* classes to style the span that displays the text, but i might be better to just use `strong` and `em` to be better included in the general rules for web.