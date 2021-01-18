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

*07.01.2020 - ~14:00*

Backporting to Typo3 Version 7.6.33 was easier than expected in most parts. There were some major functions that behaved differently in this version but i was able to overcome those challenges. Some of my mitigations did not worked out they way i planned and i am still baffled by this. Every piece of information i could find indicated that it should the work the way it doesn't. Especially the Fluid `f:variable` component stings, it should have been replaceable with `vhs` but fluid script doesn't want to accept it. In a sense this isn't all that terrible cause it keeps the amount of 3rd party plugins needed down. Still, the implementation is now in the php part which is a tiny bit dirty.

*15.01.2020 - 11:21*

The actual coding progress slowed down as i encountered a new kind of problem. I knew that there will always be a usability problem ahead but it seems bigger than i thought. The general way this plugin functions is quite simple and apart from the front end part which might need some additional tweaking i could easily call it finished and be done with it. Unfortunately this would not meet my own standards as i aim to make the thing actual usable by the common folk. As of now it would be quite cumbersome to get any meaningful data displayed on a website without having looked deeply in the source you want to mirror. There is a huge need for easy to use functions, to create those i need time that i not measure in written lines of code.

*18.01.2020 13:00*

By now there is more than instance where i used an old value to do things that are originally not planned to be done. In the `datapoint` Model is a field named `cachedValue`, originally this was where the displayed text for the entry was held. Later on i realized that there is more than one universal language on this world and i changed the system, but the database model and all the other things where already modeled. In the database description the field is a text and will be always empty, in PHP on the other hand it can be whatever it want to be. I cannot persist it to be saved in the database obviously, that would clash with its definition. But it turns out that i got a nice avenue to seed requests with additional data. I could do it the hard way with a lot of ifs and additional loops in the front end, but the way i am using it right now is that, every time i got a datapoint and some other stuff i want to show i put those additional data as an array to the specific datapoint. This works way to well, and i really fear  that there is some update that will break this. And yet, it feels good in a simple way, it does what its supposed to do in a clean way. But its still a dirty solution.

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
    
    ​	Used in Styling of collection entries, created new partial for uniform usage
    
    `<f:variable>` does not yet exists, instead `v:variable` from the vhs ViewHelper Package can be used but required another requirement. **CHECK IF THIS IS OKAY**
    
    While the fluid `f:switch` operation already exists `f:defaultCase` does not. Easily circumvented annoying and a bit less clean. Also there seems to be a *bug* that prevents templates from being cached if a `f:switch` exists. I moved the logic in the php-logic by manipulating the object.
  
* *FrontController.php*
  
  * The Collection Choose Dialog in the plugin setup uses a ConnectionPool Query to get the available collections, for some reasons a normal storage query isn't possible in that case. I concede that i really don't know why that is the way it is. Replaced with `$GLOBALS['db']` connection query.
  
* *Styling*

  * The iterator Check like `iterator % 3 == 0` isn't working, supposedly because the `%` Operation is not yet implemented in this version of Fluid
  * Styles can be included with a `<f:section>` in a single front end element/fluid template. This works since **Typo3 8.7**, therefor it wont work in 7.6.33 this  way.
    * **ONGOING**
