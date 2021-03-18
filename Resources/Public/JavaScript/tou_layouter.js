//const KONST_TOU_VERSION = "0.7.7.1";

var active = false;
var activeItem = null;
var activeGrid = null;
var activeMode = null;
var container = null;

const KONST_CLASS_target_area = "tou_target_field";
const KONST_CLASS_outer_box = "tou_containment";
const KONST_CLASS_content_PREFIX = "tou_";
const KONST_ID_default_target_area = "tou_target_area_default";
const KONST_ID_staging_area = "tou_assembly";
const KONST_ID_event_space = "tou_layouter"
const KONST_ID_the_grid = "tou_bench";
const KONST_ID_repository = "tou_warehouse";
const KONST_ID_form = "tou_form";
const KONST_BORDER_dragbox = 7;
const KONST_WIDTH_dragbox = 150 + KONST_BORDER_dragbox * 2;
const KONST_HEIGHT_dragbox = 20 + KONST_BORDER_dragbox * 2;
const KONST_MARGIN_targetfield = 6;
const KONST_GAP_targetfield  = 8;

docReady(function() {
    spawnVersionInfo();
    container = document.querySelector(`#${KONST_ID_event_space}`);
    container.addEventListener("mousedown", dragStart, false);
    container.addEventListener("mouseup", dragEnd, false);
    container.addEventListener("mousemove", drag, false);
    container.addEventListener("touchstart", dragStart, false);
    container.addEventListener("touchend", dragEnd, false);
    container.addEventListener("touchmove", drag, false);
    document.querySelector(`#${KONST_ID_form}`).addEventListener('submit', function(event) {
        SnapTo1_1(document.querySelector(`#${KONST_ID_the_grid}`))
        //this will also trigger a mousedown & mouseup event
    }, false);
    let dragBoxes = document.querySelectorAll(`#${KONST_ID_repository} .${KONST_CLASS_outer_box}`)
    for( const box of dragBoxes) {
        spawnDragBox(box);
    }
    let setBoxes = document.querySelectorAll(`#${KONST_ID_the_grid} > div`);
    let doOnce = true;
    if( setBoxes.length === 0 ) {
        origin = document.createElement("div");
        origin.className = KONST_CLASS_target_area;
        origin.style.gridArea = "2 / 2 / 3 / 3";
        origin.id = KONST_ID_default_target_area;
        document.querySelector(`#${KONST_ID_the_grid}`).append(origin);
    }
    for( const box of setBoxes ) {
        let dragBox = box.innerHTML;
        box.innerHTML = "";
        box.className = KONST_CLASS_target_area
        tou_gridTranslateXY(box, 1,1);
        if( doOnce ) { box.id = KONST_ID_default_target_area; doOnce = false; }
        let seedBox = document.createElement("div");
        seedBox.className = KONST_CLASS_outer_box;
        seedBox.innerHTML = dragBox;
        box.appendChild(seedBox);
        spawnDragBox(seedBox);
        //resize boxes to grid:
        discretWidth = parseInt(box.style.gridColumnEnd) - parseInt(box.style.gridColumn);
        discretHeight = parseInt(box.style.gridRowEnd) - parseInt(box.style.gridRow);
        setDragBoxDimension(seedBox, discretWidth*KONST_WIDTH_dragbox, discretHeight*KONST_HEIGHT_dragbox);
    }
});

function docReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

function spawnDragBox(el) {
    let classes = [`${KONST_CLASS_content_PREFIX}corner_lt`, `${KONST_CLASS_content_PREFIX}border_t`, `${KONST_CLASS_content_PREFIX}corner_rt`,
        `${KONST_CLASS_content_PREFIX}border_l`, `${KONST_CLASS_content_PREFIX}content_box`,
        `${KONST_CLASS_content_PREFIX}border_r`, `${KONST_CLASS_content_PREFIX}corner_lb`,
        `${KONST_CLASS_content_PREFIX}border_b`, `${KONST_CLASS_content_PREFIX}corner_rb`];
    let contentBox = el.innerHTML;
    el.innerHTML = "";
    classes.forEach( function(item, idx ) {
        let tempDiv = document.createElement('div');
        tempDiv.className = item;
        if( item === `${KONST_CLASS_content_PREFIX}content_box` ) { tempDiv.innerHTML = contentBox;}
        el.appendChild(tempDiv)
    });
}

function dragStart(e) {
    e.preventDefault;
    if (e.target.parentElement !== e.currentTarget.parentElement && e.button === 0) {
        if( recursiveCheck(e.target, [KONST_CLASS_outer_box])) {
            active = true;
            switch( e.target.className ) {
                case `${KONST_CLASS_content_PREFIX}corner_lt`: activeMode = 1; break;
                case `${KONST_CLASS_content_PREFIX}corner_rt`: activeMode = 2; break;
                case `${KONST_CLASS_content_PREFIX}corner_rb`: activeMode = 3; break;
                case `${KONST_CLASS_content_PREFIX}corner_lb`: activeMode = 4; break;
                case `${KONST_CLASS_content_PREFIX}border_t`: activeMode = 5; break;
                case `${KONST_CLASS_content_PREFIX}border_r`: activeMode = 6; break;
                case `${KONST_CLASS_content_PREFIX}border_b`: activeMode = 7; break;
                case `${KONST_CLASS_content_PREFIX}border_l`: activeMode = 8; break;

                default: //or content_box
                    e.target.style.cursor ="grabbing";
                    activeMode = 0;
                    break;
            }
            try {
                activeItem = recursiveSelect(e.target, KONST_CLASS_outer_box)
            } catch(e) {
                activeItem = null;
                active = false;
                return false; // breaks function, value of no import
            }
            if( activeItem.parentElement.parentElement.id !== KONST_ID_the_grid )
            { activeGrid = null;}
            else { activeGrid = activeItem.parentElement; }
            //initialize vars
            if( activeItem !== null) {
                if (!activeItem.xOffeset) {
                    activeItem.xOffset = 0;
                }
                if (!activeItem.yOffset) {
                    activeItem.yOffset = 0;
                }
            }
            if( activeMode === 0 ) {
                if (e.type === "touchstart") {
                    activeItem.initialX = e.touches[0].clientX - activeItem.xOffset;
                    activeItem.initialY = e.touches[0].clientY - activeItem.yOffset;
                }
                else {
                    activeItem.initialX = e.clientX - activeItem.offsetWidth/2
                    activeItem.initialY = e.clientY - activeItem.offsetHeight/2
                }
                activeItem.style.position = "fixed";
                setTranslate(activeItem.initialX, activeItem.initialY, activeItem);
                //reset stuff
                resetGridTo1x1(activeItem.parentElement);
                setDragBoxDimension(activeItem, KONST_WIDTH_dragbox, KONST_HEIGHT_dragbox);
                document.querySelector(`#${KONST_ID_repository}`).append(activeItem);
            }
            if( activeMode === 6 ) {
                if( activeItem.parentElement.tou_hasClass(KONST_CLASS_target_area) ) {
                    MakeElementAbsolute(activeItem);
                    activeItem.initialX = e.clientX - activeItem.offsetWidth;
                }
            }
            if( activeMode === 7 ) {
                if( activeItem.parentElement.tou_hasClass(KONST_CLASS_target_area) ) {
                    MakeElementAbsolute(activeItem);
                    activeItem.initialY = e.clientY - activeItem.offsetHeight;
                }
            }
        }
    }

}

function dragEnd(e) {
    let snapBack = true;
    if (activeItem === null) { return null; } //abort if nothing is to be done

    activeItem.initialX = activeItem.currentX;
    activeItem.initialY = activeItem.currentY;

    //resetCursor
    if( activeMode === 0 ) {
        e.target.style.cursor = "grab";
        hoverElements = document.elementsFromPoint(e.clientX, e.clientY)
        for( const val of hoverElements ) {
            if( val.className === KONST_CLASS_target_area ) {
                val.appendChild(activeItem);
                if(activeGrid !== null && val !== activeGrid ) {
                    //if target grid is default grid, resets that grid (cause it doesnt get deleted like the others)
                    setDragBoxDimension(activeItem, KONST_WIDTH_dragbox, KONST_HEIGHT_dragbox);
                    if( activeGrid.id === KONST_ID_default_target_area ) {
                        activeGrid.style.gridColumnEnd = parseInt(activeGrid.style.gridColumnStart) + 1
                        activeGrid.style.gridRowEnd = parseInt(activeGrid.style.gridRowStart) + 1
                    }
                }
                snapBack = false;
                break;
            }
        }
        if( snapBack ) {
            document.querySelector(`#${KONST_ID_repository}`).append(activeItem);
            setDragBoxDimension(activeItem, KONST_WIDTH_dragbox, KONST_HEIGHT_dragbox);
            let boxInput = activeItem.querySelector(`input`);
            if( boxInput !== null ) {
                boxInput.value = "";
            }
            if( activeGrid !== null && activeGrid.id === KONST_ID_default_target_area ) {
                activeGrid.style.gridColumnEnd = parseInt(activeGrid.style.gridColumnStart) + 1
                activeGrid.style.gridRowEnd = parseInt(activeGrid.style.gridRowStart) + 1
            }
        }
        activeItem.style = null;
    }

    if( activeMode === 1) {
        tou_fadeOut(activeItem, 1200)
        setTimeout(tou_fadeIn(activeItem, 1200, "grid"), 2500);
    }

    if( activeMode === 6) { //snap to width
        let steps = Math.round(activeItem.offsetWidth / KONST_WIDTH_dragbox);
        let newWidth = steps*KONST_WIDTH_dragbox
        if( newWidth <= 0 ) { newWidth = KONST_WIDTH_dragbox; }
        activeItem.style.position = "";
        activeItem.style.left = "";
        activeItem.style.top = "";
        if( gridCheckLineCollision(activeItem.parentElement, "right", steps-1) ) {
            activeItem.parentElement.style.gridColumnEnd = parseInt(activeItem.parentElement.style.gridColumnStart) + steps
            setDragBoxDimension(activeItem, newWidth);
        }
        else {
            setDimension(activeItem, activeItem.addInfo.width, activeItem.addInfo.height);
        }
    }
    if( activeMode === 7) { //snap to height
        let steps = Math.round(activeItem.offsetHeight / KONST_HEIGHT_dragbox);
        let newHeight = steps*KONST_HEIGHT_dragbox
        if( newHeight <= 0 ) { newHeight = KONST_HEIGHT_dragbox; }
        activeItem.style.position = "";
        activeItem.style.left = "";
        activeItem.style.top = "";
        if( gridCheckLineCollision(activeItem.parentElement, "bottom", steps-1) ) {
            activeItem.parentElement.style.gridRowEnd = parseInt(activeItem.parentElement.style.gridRowStart) + steps
            setDragBoxDimension(activeItem, 0, newHeight);
            //setDimension(activeItem, 0, newHeight);
        }
        else {
            setDimension(activeItem, activeItem.addInfo.width, activeItem.addInfo.height);
        }
    }

    //clear out placeholders
    nodeList = document.querySelectorAll(`#${KONST_ID_repository} .${KONST_CLASS_outer_box}`);
    for( const field of nodeList) {
        if( field.tou_hasClass('placeholder') ) {
            field.remove();
        }
    }
    let newPlaceholder = document.createElement('div')
    newPlaceholder.className = KONST_CLASS_outer_box + " placeholder";
    newPlaceholder.innerText = "Platzhalter";
    spawnDragBox(newPlaceholder);
    document.querySelector(`#${KONST_ID_repository}`).appendChild(newPlaceholder);
    //clear empty fields in target grid:
    //&&update internal form fields
    nodeList = document.querySelector(`#${KONST_ID_the_grid}`).querySelectorAll(`.${KONST_CLASS_target_area}`);
    for( const divField of nodeList ) {
        if( divField.tou_isEmpty() && divField.id !== KONST_ID_default_target_area) {
            tou_fadeOut(divField, 600, true);
            continue;
        }
        else{ // in case of major changes this is a likely source of problems cause it selects any one input per div
            let boxInput = divField.querySelector(`input`);
            if( boxInput !== null ) {
                boxInput.value = divField.style.gridArea;
            }

        }
    }
    setTimeout(function() {
        //move entire grid one to  the right/bottom if any field is 1 / 1
        //nodeList is static so it needs to be retrieved anew
        nodeList = document.querySelector(`#${KONST_ID_the_grid}`).querySelectorAll(`.${KONST_CLASS_target_area}`);
        let moveRight = false;
        let moveBot = false;
        for( const divNode of nodeList ) {
            if( parseInt(divNode.style.gridColumnStart) === 1 ) {moveRight = true;}
            if( parseInt(divNode.style.gridRowStart) === 1 ) {moveBot = true;}
        }
        if( moveRight || moveBot ) {
            moveRight = moveRight ? 1 : 0;
            moveBot = moveBot ? 1 : 0;
            for( const divNode of nodeList ) {
                tou_gridTranslateXY(divNode, moveRight, moveBot);
                /*divNode.style.gridColumnStart = parseInt(divNode.style.gridColumnStart) + moveRight
                divNode.style.gridColumnEnd = parseInt(divNode.style.gridColumnEnd) + moveRight
                divNode.style.gridRowStart = parseInt(divNode.style.gridRowStart) + moveBot
                divNode.style.gridRowEnd = parseInt(divNode.style.gridRowEnd) + moveBot*/
            }
        }
        cleanUpGrid(document.querySelector(`#${KONST_ID_the_grid}`));
    }, 800); //anonymous function from above

    active = false;
    activeItem = null;
    activeGrid = null;
    activeMode = null;
}

function drag(e) {
    if( active ) {

        e.preventDefault();
        console.log(e.clientX, e.clientY);

        if( activeMode === 0 ) {
            if (e.type === "touchmove") {
                activeItem.currentX = e.touches[0].clientX - activeItem.initialX;
                activeItem.currentY = e.touches[0].clientY - activeItem.initialY;
            }
            else {
                activeItem.currentX = e.clientX - activeItem.initialX;
                activeItem.currentY = e.clientY - activeItem.initialY;
            }

            activeItem.xOffset = activeItem.currentX;
            activeItem.yOffset = activeItem.currentY;

            let MiddleX = e.clientX - activeItem.offsetWidth/2;
            let MiddleY = e.clientY - activeItem.offsetHeight/2;

            //setTranslate(activeItem.currentX, activeItem.currentY, activeItem);
            setTranslate(MiddleX, MiddleY, activeItem);
            handleGridLayouter(e);
        }

        if( activeMode === 6 ) {
            let newWidth = e.clientX - activeItem.initialX;
            if( newWidth > KONST_WIDTH_dragbox ) {
                activeItem.style.left = `${activeItem.addInfo.left - ((activeItem.addInfo.width-newWidth)/2)}px`;
                setDimension(activeItem, newWidth, 0 );
            }
        }
        if( activeMode === 7 ) {
            let newHeight = e.clientY - activeItem.initialY;
            if( newHeight > KONST_HEIGHT_dragbox ) {
                activeItem.style.top = `${activeItem.addInfo.top - ((activeItem.addInfo.height-newHeight)/2)}px`;
                setDimension(activeItem, 0, newHeight);
            }
        }

    }
}

function setTranslate(xPos, yPos, el) {
    //el.style.transform = "translate3d(" + xPos + "px, " + yPos + "px, 0)";
    el.style.left = xPos;
    el.style.top = yPos;
}

function setDimension(el, width = 0, height = 0) {
    if( width !== 0 ) { el.style.width = width; }
    if( height !== 0 ) { el.style.height = height; }
}

function setDragBoxDimension(el, width = 0, height = 0) {
    let w_steps = width === 0 ? 0 :  ( width / KONST_WIDTH_dragbox ) -1; //every additional length beyond the first
    let h_steps = height === 0 ? 0 : ( height / KONST_HEIGHT_dragbox ) -1;
    //when a grid element is longer than one row/column we need to factor in the gap
    //which consists of 2 times half the margin and one time the actual gap per multiple of dimension
    let gapWidth = KONST_GAP_targetfield*w_steps + KONST_MARGIN_targetfield * w_steps
    let gapHeight = KONST_GAP_targetfield*h_steps + KONST_MARGIN_targetfield * h_steps
    setDimension(el, width + gapWidth, height + gapHeight); // the overall box you drag around
    contentBox = el.querySelector(`.${KONST_CLASS_content_PREFIX}content_box`);
    let innerWidth = width === 0 ? 0 : width - KONST_BORDER_dragbox * 2 + gapWidth;
    let innerHeight = height === 0 ? 0 : height - KONST_BORDER_dragbox * 2 + gapHeight;
    setDimension(contentBox, innerWidth, innerHeight); // text box dimension minus border
    //the outer container (with the dashed border)
    /*
    //apparently resizes itself just fine with normal css without me doing things
    let grid = recursiveSelect(el, KONST_CLASS_target_area);
    if( w_steps > 0) {
        grid.style.width = width + KONST_MARGIN_targetfield + (w_steps * KONST_MARGIN_targetfield) + (w_steps * KONST_GAP_targetfield);
    }
    if( h_steps > 0 ) {
        grid.style.height = height + KONST_MARGIN_targetfield + (h_steps * KONST_MARGIN_targetfield) + (h_steps * KONST_GAP_targetfield)
    }*/
}

function MakeElementAbsolute(el) {

    //this only really works with grid elements
    //https://webdesign.tutsplus.com/tutorials/the-quirks-of-css-grid-and-absolute-positioning--cms-31437
    el.addInfo = {left: parseInt(el.offsetLeft), top: parseInt(el.offsetTop),
        width: parseInt(el.offsetWidth), height: parseInt(el.offsetHeight)};
    el.style.position = "absolute";
    el.style.zIndex = 15;
    el.style.left = `${el.addInfo.left}px`;
    el.style.top = `${el.addInfo.top}px`;
}

function handleGridLayouter(e) {
    hoverElements = document.elementsFromPoint(e.clientX, e.clientY)
    let overTarget = false;
    let insideStaging = false;
    for( const val of hoverElements ) {
        if( val.className === KONST_CLASS_target_area ) {
            //do things with the thing
            overTarget = true;
        }

        if( val.id === KONST_ID_staging_area ) { insideStaging = true;}
    }

    if( insideStaging && !overTarget ) {
        let staging = document.querySelector('#'+KONST_ID_staging_area);
        let nodeList = staging.querySelectorAll('.'+KONST_CLASS_target_area);
        let currentClosest = 40962048.0; //one of those magic numbers that hopes that the UltraHiDPI Display never gets invented
        let closestElement = null;
        let angle = 0.0;
        //correct for page scroll:
        Mpoint = {x: e.clientX + window.pageXOffset, y: e.clientY + window.pageYOffset};
        for( const node of nodeList) {
            if( node.tou_isEmpty() ) { continue; }
            //node.style.outline = "1px dashed #FF06B5";
            let coords = getCoords(node);
            let tempDist = getBoxDistance2Point(
                Mpoint.x, Mpoint.y,
                coords.left, coords.top, coords.right, coords.bottom);
            if( tempDist < currentClosest ) {
                currentClosest = tempDist;
                closestElement = node;
                angle = getBox2PointDirection(Mpoint.x, Mpoint.y,
                    coords.left, coords.top, coords.right, coords.bottom);
            }
        }
        if( closestElement ) {
            //closestElement.style.outline = "1px dotted green";
            let newGridCoordinates = gridCoordinates(closestElement, angle)
            let gridTest = gridCollisionCheck( document.querySelector(`#${KONST_ID_the_grid}`), newGridCoordinates);
            if( gridTest === 0 ) {
                insertGridField(document.querySelector(`#${KONST_ID_the_grid}`), newGridCoordinates);
            }
            else if( Math.abs(gridTest) === 2) { //grid is already in place BUT not empty so we move everything
                ShiftGrid(closestElement, angle)
                insertGridField(document.querySelector(`#${KONST_ID_the_grid}`), newGridCoordinates);
            }

        }
    }
}

function insertGridField(target, coordinates) {
    if( coordinates.trim() === "" ) { return false;}
    let nodes = target.querySelectorAll(`.${KONST_CLASS_target_area}`);
    for( const div of nodes) {
        if( div.style.gridArea === coordinates ) { return false;}
    }
    let brandNew = document.createElement('div')
    brandNew.className = KONST_CLASS_target_area;
    brandNew.style.gridArea = coordinates;
    target.appendChild(brandNew);
    tou_fadeIn(brandNew, 600, "grid-item");
}

/**
 * Shifts part of the grid elements in one direction to make
 * room for a new grid element, direction gives space
 *
 * @param {Element} pivotElement    the element of which of the change starts
 * @param {number} direction        the direction of change, 0:left, 1: right, 2: top, 3: bot
 *
 * @return {boolean} true on succesful, false when encountering a problem
 */
function ShiftGrid(pivotElement, direction)
{
    let nodeList = pivotElement.parentElement.children;
    let shiftX = 0;
    let shiftY = 0;
    if( direction === 0 || direction === 1 ) {
        shiftX = 1;
    } else {
        shiftY = 1;
    }
    if( direction === 0 || direction === 2 ) {
        pX = parseInt(pivotElement.style.gridColumnStart);
        pY = parseInt(pivotElement.style.gridRowStart);
    }
    else {
        pX = parseInt(pivotElement.style.gridColumnEnd) + 1;
        pY = parseInt(pivotElement.style.gridRowEnd) + 1;
    }
    for(const node of nodeList ) {
        tX = parseInt(node.style.gridColumnStart);
        tY = parseInt(node.style.gridRowStart);
        if( tX >= pX) {
            tou_gridTranslateX(node,shiftX);
        }
        if( tY >= pY) {
            tou_gridTranslateY(node, shiftY);
        }
    }

    return true;
}

/**
 * Removes empty columns & rows from a grid structure by compressing & manipulating those together
 *
 * It uses the gridCollisionCheck Method which in itself iterates through the entire grid
 * i assume that this might be somewhat costly on bigger grids cause it does that for every
 * probing grid-element of which n + m are used where n&m are the number of rows and columns
 * the grid posseses (excluding the always empty 1/1 lanes).
 *
 * *Edit: i measured it, it took around a millisecond for 3 empty lanes on a 9750H with FF 86.0*
 *
 * @param {Element} gridElement probably a div that has **display: grid** and contains grid-items
 *
 * @return {boolean} True if something was cleaned up, if nothing happened
 */
function cleanUpGrid(gridElement) {
    //the only time i am using 'var' in this, let would have worked just well but i am nostalgic
    var maxCol = tou_getComputedColumns(gridElement);
    var maxRow = tou_getComputedRows(gridElement);
    var workDone = 0;
    //the grid starts at 2/2 cause 1/1 is reserved for new columns and has to be free
    /* Free Column Check */
    for(let x = 2; x <= maxCol; x++) {
        let y = 2;
        let checkCoords = `${y} / ${x} / ${y+1} / ${x+1}`;
        if( gridCollisionCheck(gridElement, checkCoords) === 0 ) { //free field
            let actUpon = true;
            for( y = 2; y <= maxRow; y++) {
                let checkCoords = `${y} / ${x} / ${y+1} / ${x+1}`;
                if( gridCollisionCheck(gridElement, checkCoords) !== 0 ) { actUpon = false; break; }
            }
            if( actUpon ) {
                let nodeList = gridElement.children;
                for( const node of nodeList) {
                    pX = parseInt(node.style.gridColumnStart);
                    if( pX > x ) { tou_gridTranslateX(node, -1); workDone++;}
                }
            }
        }
    }
    /* Free Row Check */
    for(let y = 2; y <= maxRow; y++) {
        let x = 2;
        let checkCoords = `${y} / ${x} / ${y+1} / ${x+1}`;
        if( gridCollisionCheck(gridElement, checkCoords) === 0 ) {
            let actUpon = true;
            for( x = 2; x <= maxCol; x++) {
                let checkCoords = `${y} / ${x} / ${y+1} / ${x+1}`;
                if( gridCollisionCheck(gridElement, checkCoords) !== 0 ) { actUpon = false; break; }
            }
            if( actUpon ) {
                let nodeList = gridElement.children;
                for( const node of nodeList) {
                    pY = parseInt(node.style.gridRowStart);
                    if( pY > y ) { tou_gridTranslateY(node, -1); workDone++;}
                }
            }
        }
    }
    if( workDone >= 0 ) { return true;}
    else { return false;}
}
function gridCollisionCheck(container, newCoordinates) {
    let dummyObject = document.createElement('div');
    dummyObject.style.gridArea = newCoordinates;
    let dummyDimensions = {
        lx: parseInt(dummyObject.style.gridColumnStart),
        ly: parseInt(dummyObject.style.gridRowStart),
        rx: parseInt(dummyObject.style.gridColumnEnd),
        ry: parseInt(dummyObject.style.gridRowEnd)
    }

    for( const node of container.children ) {
        //let overlap = 0;
        let nodeDimensions = {
            lx: parseInt(node.style.gridColumnStart),
            ly: parseInt(node.style.gridRowStart),
            rx: parseInt(node.style.gridColumnEnd),
            ry: parseInt(node.style.gridRowEnd)
        }

        /*due the nature of grids and its full values we dont check for <=/>= cause
        the its okay to be edge to edge for a grid */

        if( nodeDimensions.lx === dummyDimensions.lx &&
            nodeDimensions.ly === dummyDimensions.ly &&
            nodeDimensions.rx === dummyDimensions.rx &&
            nodeDimensions.ry === dummyDimensions.ry )
        { if( node.tou_isEmpty() ) {return -1;} else {return -2;} }

        /*Grids are a bit weird, they are rectangles with a zero width border (when seen as grid coordinates)
        that means they are not touching if their edges overlap, that means i need to adjust some
        treshhold conditions, like one rectangle being inside the other while having the exact
        coordinates of the walls, which is a really like case*/

        if( nodeDimensions.lx <= dummyDimensions.lx &&
            nodeDimensions.rx >= dummyDimensions.rx &&
            nodeDimensions.ly <= dummyDimensions.ly &&
            nodeDimensions.ry >= dummyDimensions.ry)
        {  if( node.tou_isEmpty() ) {return 1;} else {return 2;} }
        /*
        So, a few words to this, the math behind this is quite simple, i must confess i still struggled a bit
        to wrap my hand around it all. So, every grid-element is a rectangle with a left-upper and right-lower corner
        this checks if we can create a new rectangle in a grid with the given coordinates, originally i checked
        if our new rectangle cuts into any of the others, which grids do quite often cause they are defined by
        corner points but with a border thickness of zero (not the css border), therefore the normal boundary
        check does not work. I tried to mitigate that with greater than instead of greater equal than, but that
        doesnt account for the case where alle 4 points are within another grid-item (cause its bigger than 1x1)
        BUT, every new grid item is always 1x1, so i dont need to do the checks below (there is some error there anyway)
        so i am not doing all the things below, but i am leaving this long note for you dear reader, whoever you are.
        this is a public comment and it consumes so many delicious bytes, but sometimes i wish the world would write
        more records like this, tiny novellas to describe the history of things
        */
        /*

        if( dummyDimensions.lx > nodeDimensions.rx ||
            nodeDimensions.lx > dummyDimensions.rx)
        { overlap++;  }

        if( dummyDimensions.ly > nodeDimensions.ry ||
            nodeDimensions.ly > dummyDimensions.ry)
        { overlap++; }

        if( overlap > 1 ) {
            setNote(`boundary violation: ${nodeDimensions.lx};${nodeDimensions.ly}|${nodeDimensions.rx};${nodeDimensions.ry} ~~~ ${dummyDimensions.lx};${dummyDimensions.ly}|${dummyDimensions.rx};${dummyDimensions.ry}`);
            if( node.tou_isEmpty() ) {return 1;} else {return 2;}
        }
        */

    }
    return 0;
}

function gridCheckLineCollision(sourceElement, direction, iterations = 1) {
    let others = {'l': 0, 'r': 1, 't': 2, 'b': 3,
        'left': 0, 'right': 1, 'top': 2, 'bottom': 3 }
    Object.keys(others).forEach(function(key, index) {
        if( direction === key ) { direction = this[key]; return true;} }, others);

    //some sanity checks
    iterations = iterations <= 0 ? 1 : iterations;
    iterations = iterations >= 10 ? 10 : iterations;
    let parent = sourceElement.parentElement;
    let colStart = parseInt(sourceElement.style.gridColumnStart);
    let colEnd = parseInt(sourceElement.style.gridColumnEnd);
    let rowStart = parseInt(sourceElement.style.gridRowStart);
    let rowEnd = parseInt(sourceElement.style.gridRowEnd);
    let x = 0; let y = 0; //init
    for( let i = 0; i < iterations; i++) {
        switch( direction ) {
            case 0: //left
                x = colStart - 1 - i;
                if( x < 1 ) { return false; }
                for(y = rowStart; y < rowEnd; y++) {
                    if( gridCollisionCheck(parent, `${y} / ${x} / ${y+1} / ${x+1}`) !== 0 ) {return false;}
                }
                break;
            case 1: //right
                x = colEnd + i;
                if( x < 1 ) { return false; } //should never be a problem in plus direction
                for(y = rowStart; y < rowEnd; y++) {
                    if( gridCollisionCheck(parent, `${y} / ${x} / ${y+1} / ${x+1}`) !== 0 ) {return false;}
                }
                break;
            case 2: //top
                y = rowStart - 1 - i;
                if( y < 1 ) { return false; }
                for(x = colStart; x < colEnd; x++) {
                    if( gridCollisionCheck(parent, `${y} / ${x} / ${y+1} / ${x+1}`) !== 0 ) {return false;}
                }
                break;
            case 3: //bottom
                y = rowEnd + i;
                if( y < 1 ) { return false; } // again "unpossible"
                for(x = colStart; x < colEnd; x++) {
                    if( gridCollisionCheck(parent, `${y} / ${x} / ${y+1} / ${x+1}`) !== 0 ) {return false;}
                }
                break;
            default:
                return false;
        }
    }
    return true;
}

function getCoords(elem) {
    // https://javascript.info/coordinates
    let box = elem.getBoundingClientRect();

    return {
        top: box.top + window.pageYOffset,
        right: box.right + window.pageXOffset,
        bottom: box.bottom + window.pageYOffset,
        left: box.left + window.pageXOffset
    };
}

function getBoxDistance2Point(Px, Py, Left, Top, Right, Bottom) {
    //Px, Py, Top, Left, Bottom, Right
    /*
     X1/Y1
        ------------
        |          |
        |          |    x
        |          |     PX/PY
        ------------
                    X2/Y2
    */
    //assuming said box isnt rotated, aligning to a grid
    //tbh i struggled a bit to see the math behind this at first, so this is two times the same thing
    //let dx = Math.max(Left - Px, 0, Px - Right);
    //let dy = Math.max(Top - Py, 0, Py - Bottom);
    let cx = Math.max(Math.min(Px, Right), Left);
    let cy = Math.max(Math.min(Py, Bottom), Top);
    //return
    //let temp = Math.sqrt(dx*dx + dy*dy);
    //let temp2 = Math.sqrt((Px-cx)*(Px-cx) + (Py-cy)*(Py-cy))
    //console.log(`Point [${Px};${Py}] Box[[${Left};${Top}][${Right};${Bottom}]] -> ${temp} vs ${temp2}`);
    return Math.sqrt((Px-cx)*(Px-cx) + (Py-cy)*(Py-cy))
    // Math.hypot(dx, dy) in case of extreme high dx/dy
}

function getBox2PointDirection(Px, Py, Left, Top, Right, Bottom) {
    //if you use the mouse pointer as Px/Py, remember to adjust for scrolling
    let MidX = Left + (Right-Left)/2
    let MidY = Top + (Bottom-Top)/2
    let angle = Math.atan2(Py - MidY, Px - MidX) * 180 / Math.PI;
    /*document.querySelector('#roterPunkt').style.left = Px;
    document.querySelector('#roterPunkt').style.top = Py;*/

    let dir = 0;

    if( angle >= -135.0 && angle < -45.0 ) { dir = 2; }
    else if ( angle >= -45.0 && angle < 45.0 ) { dir = 1; }
    else if ( angle >= 45.0 && angle < 135.0 ) { dir = 3; }
    else { dir = 0;}
    return dir;
}

/**
 * Gives a CSS grid-area-value for a new field relative to a given grid-element.
 *
 * @param {Element} el  an DOM element that got grid-element coordindates
 * @param {number} direction     0 = left, 1 = right, 2 = top, 3 = bottom, literals possibles, eg: "right" or "r"
 *
 * @return {string} grid-area-value: eg: "1 / 1 / 2 / 1"
 */
function gridCoordinates(el, direction) {
    let others = {'l': 0, 'r': 1, 't': 2, 'b': 3,
        'left': 0, 'right': 1, 'top': 2, 'bottom': 3 }
    Object.keys(others).forEach(function(key, index) {
        if( direction === key ) { direction = this[key]; return true;} }, others);
    //sometimes i have the feeling i am doing things to complicated

    let left = parseInt(el.style.gridColumnStart);
    left = isNaN(left) ? 0 : left;
    let top = parseInt(el.style.gridRowStart);
    top = isNaN(top) ? 0 : top;
    let right = parseInt(el.style.gridColumnEnd);
    right = isNaN(right) ? 0 : right;
    let bottom = parseInt(el.style.gridRowEnd);
    bottom = isNaN(bottom) ? 0 : bottom;
    switch( direction) {
        case 0:
            return `${top} / ${left-1} / ${top+1} / ${left}`;
        case 1:
            return `${bottom-1} / ${right} / ${bottom} / ${right+1}`;
        case 2:
            return `${top-1} / ${left} / ${top} / ${left+1}`;
        default:
            return `${bottom} / ${right-1} / ${bottom+1} / ${right}`;
    }
}

//tools and functionality

/** recursiveHit
 *
 * Checks if somewhere in the Tree down to the root is an element with one of
 * the desired classes.
 *
 * @param {Element} el
 * @param {[]} classList
 * @param {number} maxIteration = 25
 *
 * @return {boolean}
 */
function recursiveCheck(el, classList, maxIteration = 25) {
    recursor = el;
    for( let i = 0; i < maxIteration; i++ ) {
        let parentClasses = recursor.classList;
        if( recursor.tagName === "HTML" ) { return false; }
        for ( const parentClass of parentClasses ) {
            for( const warrantClass of classList ) {
                if( parentClass === warrantClass ) {
                    return true;
                }
            }
        }
        recursor = recursor.parentElement;
    }
    return false;
}

/**
 *
 * @param {element} el
 * @param {string} searchClass
 * @param {number} maxIteration
 *
 * @return {element}
 * @throws no hit
 */
function recursiveSelect(el, searchClass, maxIteration = 25 ) {
    recursor = el;
    for( let i = 0; i < maxIteration; i++ ) {
        let elementClasses = recursor.classList;
        if( recursor.tagName === "HTML" ) { throw 'no hit till root'; }
        for( const elementClass of elementClasses ) {
            if( elementClass === searchClass ) {
                return recursor;
            }
        }
        recursor = recursor.parentElement;
    }
    throw 'no hit but iteration limit'
}

//everytime i extend Element i have the dreading feeling that i am recreating jquery
// http://perfectionkills.com/whats-wrong-with-extending-the-dom/
// it seems when IE hasnt died in the hellfire it always deserved this was a problem:
// although, dont manipulate objects you do not own, hrr

Element.prototype.tou_hasClass = function(className) {
    return this.classList.contains(className);
}

Element.prototype.tou_isEmpty = function() {
    return this.innerHTML === "";
}

//took this as inspiration but made a prototype out of it
//https://dev.to/bmsvieira/vanilla-js-fadein-out-2a6o
//https://codepen.io/jorgemaiden/pen/xoRKWN

function tou_fadeOut(elementOfSurprise, duration = 600, remove = false) {
    elementOfSurprise.style.opacity = 1;
    let last = +new Date();
    let nextTick = function () {
        elementOfSurprise.style.opacity = +elementOfSurprise.style.opacity - (new Date() - last) / duration;
        last = +new Date();
        if( +elementOfSurprise.style.opacity > 0) {
            (window.requestAnimationFrame && requestAnimationFrame(nextTick)) || setTimeout(nextTick, 16);
        }
        else if( remove === true ) {
            elementOfSurprise.remove();
        }
    };
    nextTick();
}

function tou_fadeIn(elementOfSurprise, duration = 600, display = "block") {
    elementOfSurprise.style.opacity = 0;
    elementOfSurprise.style.display = display;

    let last = +new Date();
    let nextTick = function () {
        elementOfSurprise.style.opacity = +elementOfSurprise.style.opacity + (new Date() - last) / duration;
        last = +new Date();
        if( +elementOfSurprise.style.opacity < 1) {
            (window.requestAnimationFrame && requestAnimationFrame(nextTick)) || setTimeout(nextTick, 16);
        }
    };
    nextTick();
}

/**
 * Short function to move a grid-item in the column/x direction
 *
 * @param {number} num  Number of Columns you want to move, negative possible but resulting Column cannot be smaller than 1
 * @return {boolean}    True if the translation has succeded, false if it was impossible
 */
function tou_gridTranslateX(el, num) {
    let currentColStart = parseInt(el.style.gridColumnStart);
    let currentColEnd   = parseInt(el.style.gridColumnEnd);
    if( currentColEnd + num < 1 || currentColStart + num < 1 ) {
        return false;
    }
    el.style.gridColumnStart = currentColStart + num;
    el.style.gridColumnEnd = currentColEnd + num;
    return true;
}

/**i would like to check if the new coordinates clash with any other
 grid element already present but for that i had to check the entire
 grid which sounds somewhat expensive so i dont do that*/
function tou_gridTranslateY(el, num) {
    let currentRowStart = parseInt(el.style.gridRowStart);
    let currentRowEnd   = parseInt(el.style.gridRowEnd);
    if( currentRowEnd + num < 1 || currentRowStart + num < 1 ) {
        return false;
    }
    el.style.gridRowStart = currentRowStart + num;
    el.style.gridRowEnd = currentRowEnd + num;
    return true;
}

function tou_gridTranslateXY(el, x, y) {
    if( tou_gridTranslateX(el, x) ) {
        return tou_gridTranslateY(el, y);
    }
    return false;
}

function tou_getComputedColumns(el) {
    let theGrid = window.getComputedStyle(el);
    return theGrid.getPropertyValue("grid-template-columns")
        .replace(/ 0px/g, "")
        .split(" ")
        .length;
}

function tou_getComputedRows(el) {
    let theGrid = window.getComputedStyle(el);
    return theGrid.getPropertyValue("grid-template-rows")
        .replace(/ 0px/g, "")
        .split(" ")
        .length;
}

function resetGridTo1x1(el) {
    el.style.gridRowEnd = parseInt(el.style.gridRow) + 1;
    el.style.gridColumnEnd = parseInt(el.style.gridColumn) + 1;
}

//super narrow function that only works for me in this particular case:
// div:c=target_area >> div:c=outer_box >> (div:c=content_box) >> input
function SnapTo1_1(grid) {
    gridElement = grid.querySelectorAll(`.${KONST_CLASS_target_area}`);
    for( const el of gridElement) {
        tou_gridTranslateXY(el, -1, -1);
        let outer_box = el.querySelector(`.${KONST_CLASS_outer_box}`);
        let boxInput = outer_box.querySelector(`input`);
        if( boxInput !== null ) {
            boxInput.value = el.style.gridArea;
        }
    }

}

function spawnVersionInfo() {
    if( typeof KONST_TOU_VERSION !== 'undefined' ) {
        version_info = document.createElement("DIV");
        version_info.style = "position: fixed; top: 10px; left: 10px; outline: 1px solid #fa8005; width: 110px; height: 25px; background: black; opacity: 0.25; color: #fa8005; font-family: fixed; font-weight: 1200; font-size: 16px; vertical-align: middle; align-content: center; padding: 4px;";
        version_info.innerText = "Version: " + KONST_TOU_VERSION;
        document.querySelector("HTML").append(version_info);
    }
}