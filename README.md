# Sparql Toucan - a Typo3 Extension

This Plugin aims to include data from a Linked Data Source into a Website.

It contains two parts, the module and the plugin. One includes the data in the front end in the least intrusive way possible to allow the usual customization options of typo3 while doing all steps necessarily to stay true.

On the back end site there are several functions to make the use easier. A wizard to easily explore a sparql endpoint and some assistance functions to generate views for a specific set of data.





## Cpt. Logs

*30.11.2020* - 11:25

In order to achieve the functionality to get language specific LanguagePoints in the Frontend i leveraged the deprecated feature of *CachedValue* for Datapoints. The Value in the database does nothing on its own but while rendering the front end i am manipulating the content of that field to the specific displayed language (or the default if none is found). This feels quite dirty but works exactly as expected in a neat way that is easily accessible. I am still second guessing my steps here.

*30.11.2020 - 14:10*

I have additional doubts regarding the way i am doing CollectionEntry Styles. I am currently using a set of *CSS* classes to style the span that displays the text, but i might be better to just use `strong` and `em` to be better included in the general rules for web.

*02.12.2020 - 10:14*

After spending some time with the current (static) implementation and toying around with my own structure i am quite certain that i thought to simple in the ways i planned to integrate my plug-in into the existing website structure. There are actually some tricks i could pull with custom CSS-classes which are already possible in my current design but i doubt it would ultimately lead to any desired outcome. There is the almost certain chance that the plug-in will be used by people not very knowledgeable in general web technologies, even less than me. For the moment i will put the front-end questions aside and rather concentrate on the tree-structure of the data itself. There are some unsuspecting challenges here as well. 

## Backport to Typo3 7.6.33

* *DatapointOverview.html*
  * The double key `{supplement.{for_key}}` doesnt work as desired, i get an "array expected but got string instead" despite that dynamic array index should work since many versions. I replaced the it with another foreach loop, this seems quite inefficient if the engine didnt do that before anyway under the hood
* *BackendController.php*
  * Typo3 V7 does not have the PSR Guzzle Request Framework Versions 8+ do. Instead it uses a implementation of HTTP_Request2 which is slightly different in handling.
    
    `getStatusCode()` becomes just `getStatus()`
    
    `getBody()->getContents()` is only `getBody()`
    
    There is no option to just specify payloud and retrieval method, instead `setMethod('post')->addPostParameter(array)` is used
    
  * general Fluid Stuff
    `<f:form.select.option>` does not exist yet, therefore all options have to be defined in the `options` part of the original `<f:form.select>` tag