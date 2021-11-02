// Bots
var Bots = {};
(function($) {
    var variables = {}, botlist = [];
    
    Bots.setVariable = function(name, value) {        
        variables = setVariableWrapper(name, value, variables);
    }
        
    Bots.push = function(bot) {
        console.log(bot.variables);
        var d = {
            "precatch": function(text) { return text; },
            "actions": [],
            "postcatch": function(text) { return text; },
            "functions": {},
            "synonyms": [],
            "variables": {}
        };
        bot = $.extend({}, d, bot);
        console.log(bot.variables);
        
        bot.setVariable = function(name, value) {
            bot.variables = setVariableWrapper(name, value, bot.variables);
        }
        bot.getVariable = function(name) {
            var ret = false;
            if (bot.variables.hasOwnProperty(name)) ret = bot.variables[name];
            else if (variables.hasOwnProperty(name)) ret = variables[name];
                        
            assert(ret, "Can't find variable called " + name + " for bot " + bot.name);
            
            return ret(bot);
        }
        
        bot.processActionQueue = function(message) {
            var outs = [];
            for (var x = 0; x < bot.actionQueue.length; x++) {
                var actions = bot.actionQueue[x];
                
                if (!$.isArray(actions)) actions = bot.functions[actions];
                assert(actions, "No such action function called " + actions + " for bot " + bot.name);
                
                var match = false;
                for (var i = 0; i < actions.length; i++) {
                    var action = actions[i];
                    var c = action["catch"], r = action["response"];
                    
                    if (!$.isArray(c)) c = [c];
                    if (!$.isArray(r)) r = [r];
                    
                    for (var y = 0; y < c.length; y++) {
                        var options = getSynonymOptions(c[y], bot);
                        for (var z = 0; z < options.length; z++) {
                            if (catchMatches(options[z], message, bot)) {
                                match = true;
                                break;
                            }
                        }
                    }
                    
                    if (match) {
                        
                        var response = arrayRandom(r);
                        if (typeof response !== 'object') response = { "text": response || "" };
                        
                        var from = action["from"] || "*";
                        
                        if (catchMatches(from, bot.getVariable("USER"), bot)) {
                            if (!$.isArray(response["text"])) response["text"] = [response["text"]];
                            var rtext = arrayRandom(response["text"]);
                            
                            bot.setVariable("INPUT", message);
                            var synonymed = replaceSynonyms(replaceVariables(rtext, rtext, bot), bot);
                            if (response["actions"]) {
                                bot.nextActionQueue.push(response["actions"]);
                            }
                            
                            outs.push(synonymed);
                            
                            break;
                        }
                    }
                }
                if (match) break;
            }
            //console.log(bot.actionQueue, bot.nextActionQueue);
            bot.actionQueue = bot.nextActionQueue;
            bot.nextActionQueue = [];
            //console.log(bot.nextActionQueue);
            
            return outs;
        }
        
        bot.actionQueue = [];
        bot.nextActionQueue = [];
        
        botlist.push(bot);
    }
    
    Bots.find = function(name) {
        for (var i = 0; i < botlist.length; i++) {
            if (botlist[i].name.toLowerCase() == name.toLowerCase()) return botlist[i];
        }
        assert(false, "Can't find a bot called " + name);
        return false;
    }
    
    Bots.sendMessage = function(botname, message, variables) {
        var bot = Bots.find(botname);
        
        for (var key in variables) {
            if (variables.hasOwnProperty(key)) {
                bot.setVariable(key, variables[key]);
            }
        }
        
        message = bot.precatch.call(bot, message);
        message = Bots.processActions(bot, message);
        
        if (message.length > 0) {
            message = $.map(message, function(val) {
                var post = bot.postcatch.call(bot, val);
                return replaceVariables(post, post, bot);
            });
        }
        
        bot.setVariable("PREVIOUS", bot.getVariable("USER"));
        
        return message;
    }
    
    Bots.processActions = function(bot, message) {
        var actions = bot.actions;
        
        bot.actionQueue.push(bot.actions);
        var r = bot.processActionQueue(message);
        return r;
        
        /*var outs = [];
        for (var i = 0; i < actions.length; i++) {
            var action = actions[i];
            var c = action["catch"], r = action["response"];
            
            if (!Array.isArray(c)) c = [c];
            if (!Array.isArray(r)) r = [r];
            
            var match = false;
            for (var x = 0; x < c.length; x++) {
                var options = getSynonymOptions(c[x], bot);
                for (var y = 0; y < options.length; y++) {
                    if (catchMatches(options[y], message, bot)) {
                        match = true;
                        break;
                    }
                }
            }
            
            if (match) {
                var response = arrayRandom(r);
                if (typeof response !== 'object') response = { "text": response };
                
                var from = response["from"] || "*";
                
                if (catchMatches(from, bot.getVariable("USER"), bot)) {
                    //console.log(replaceVariables(response["text"], response["text"], bot));
                    var synonymed = replaceSynonyms(replaceVariables(response["text"], response["text"], bot), bot);
                    //console.log("S: " + synonymed);
                    outs.push(synonymed);
                }
            }
        }
        
        return outs;*/
    }
    
    Bots.utils = {
        "debug": true
    };
    
    /* Utility functions */
    function log(text, important) {
        if (Bots.utils.debug) {
            var title = "[Bots] ";
            if (important) title += "[Important] ";
            console.log(title + text);
        }
    }
    function assert(value, fmsg, tmsg) {
        if (!value) {
            if (fmsg === undefined) fmsg = "Assertion failed.";
            log(fmsg, true);
        } else if (tmsg !== undefined) log(tmsg);
    }
    
    function setVariableWrapper(name, value, object) {
        if (typeof value !== 'function') {
            var plainvalue = value;
            value = function() { return plainvalue; }
        }
        
        object[name.toUpperCase()] = value;
        return object;
    }
    
    function getSynonym(original, list) {
        return arrayRandom(getSynonymList(original, list));
    }
    
    function getSynonymList(original, list) {
        assert(list !== undefined, "No such list for synonyms '" + original + "'");
        
        for (var i = 0; i < list.length; i++) {
            var slist = list[i];
            if (slist.indexOf(original) > -1) return slist;
        }
        return [original];
    }
    
    function arrayRandom(array) {
        return array[Math.floor(Math.random() * array.length)];
    }
    
    function removePunctuation(text) {
        var punctuationLess = text.replace(/[\.,-\/#!$%\^&;:{}=\-_`~()@\+\?><\[\]\+]/g, "");
        return punctuationLess.replace(/\s{2,}/g, " ");
    }
    
    function replaceVariables(text, message, bot) {
        var pieces = getByDelimiter(text, "{", "}");
                
        for (var x = 1; x < pieces.length; x += 2) {
            var piece = pieces[x];
            if (piece.charAt(0) === "=") {
                var vname = piece.slice(1);
                
                var wordpos = false;
                if (text.indexOf("*") < text.indexOf("{" + piece + "}")) {
                    wordpos = (pieces.length - x - 1) * -1;
                }
                else wordpos = x;
                
                var splitmessage = message.split(" ");
                
                if (wordpos < 0) wordpos = splitmessage.length + wordpos;
                
                var word = removePunctuation(splitmessage[wordpos]);
                if (word !== undefined) bot.setVariable(vname, word);
                
                pieces[x] = "*";
            } else {
                if (pieces[x] !== "") pieces[x] = bot.getVariable(piece);
            }
        }
        //console.log(text + ", " + pieces.join(""));
        
        return pieces.join("");
    }
    
    function replaceSynonyms(text, bot) {
        var pieces = getByDelimiter(text, "^", "^");
        //console.log(pieces);
        for (var i = 1; i < pieces.length; i += 2) {
            pieces[i] = getSynonym(pieces[i], bot.synonyms);
        }
        return pieces.join("");
    }
    function getSynonymOptions(text, bot) {
        var pieces = getByDelimiter(text, "^", "^");
        
        var synonyms = [], words = [], wcount = 0;
        for (var i = 1; i < pieces.length; i+= 2) {
            //console.log(bot);
            synonyms.push(getSynonymList(pieces[i], bot.synonyms));
            words.push(pieces[i]);
            wcount++;
        }
        
        //console.log(pieces);
        function allPossibleCases(arr) {
            if (arr.length === 1) return [arr[0]];
            
            var result = [];
            var remaining = allPossibleCases(arr.slice(1));
            for (var i = 0; i < remaining.length; i++) {
                for (var j = 0; j < arr[0].length; j++) {
                    result.push([arr[0][j]].concat(remaining[i]));
                }
            }
            return result;
        }
        //words = allPossibleCases(words);
        synonyms = getCombinations(synonyms, wcount);
        
        var results = [], wpos = 0;
        for (var i = 0; i < synonyms.length; i++) {
            var options = synonyms[i], pcopy = pieces.slice();
            for (var x = 0; x < options.length; x++) {
                pcopy[x * 2 + 1] = options[x];
            }
            results.push(pcopy.join(""));
        }
        
        
        //console.log(results, words);
        return results;
    }
    
    function getCombinations(arr, n){
        var i,j,k,elem,l = arr.length,childperm,ret=[];
        if(n == 1){
            for(var i = 0; i < arr.length; i++){
                for(var j = 0; j < arr[i].length; j++){
                    ret.push([arr[i][j]]);
                }
            }
            return ret;
        }
        else{
            for(i = 0; i < l; i++){
                elem = arr.shift();
                for(j = 0; j < elem.length; j++){
                    childperm = getCombinations(arr.slice(), n-1);
                    for(k = 0; k < childperm.length; k++){
                        ret.push([elem[j]].concat(childperm[k]));
                    }
                }
            }
            return ret;
        }
        i=j=k=elem=l=childperm=ret=[]=null;
    }
    
    function getByDelimiter(text, start, end) {
        if (text.indexOf(start) < 0 || text.indexOf(end) < 0) return [text, ""];
        
        var firstpieces = text.split(start), pieces = [];
        for (var i = 0; i < firstpieces.length; i++) {
            var secondpieces = firstpieces[i].split(end);
            pieces.push.apply(pieces, secondpieces);
        }
        assert(pieces.length > 0, "Not enough pieces in text " + text);
        
        return pieces;
    }
    
    function catchMatches(ctext, message, bot) {
        ctext = removePunctuation(replaceVariables(ctext, message, bot)).toLowerCase();
        message = removePunctuation(message).toLowerCase();
        
        console.log(message, ctext, wildCompare(message, ctext));
        return wildCompare(message, ctext);
    }
    
    function wildCompare(string, search) {
        if (search.indexOf('*') < 0 && string != search) return false;
        if ((string.length < 1 && search.length > 0) || (search.length < 1 && string.length > 0)) return false;
        
        var startIndex = 0, array = search.split('*');
        for (var i = 0; i < array.length; i++) {
            var index = string.indexOf(array[i], startIndex);
            if (index == -1) return false;
            else startIndex = index;
        }
        return true;
    }
}(jQuery));

// navbar
const navbarInit = () => {
    const navbar = document.querySelector('#navbar');
    if (!navbar) return false;
    const navbarTop = document.querySelector('#navbar-top');
    const navbarBottom = document.querySelector('#navbar-bottom');
    const navbarBaseHeight = navbarTop.offsetHeight;
    const navbarTopBaseHeight = navbarTop.clientHeight;
    const offset = 20;
    const onWindowScroll = function() {
        const requiredClass = ['fixed', 'w-full', 'z-20', 'top-0', 'left-0']
        if (window.scrollY > navbarBottom.offsetHeight + offset) {
            const result = (navbarTopBaseHeight + navbarBottom.clientHeight);
            if (navbarTop.clientHeight !== result) {
                navbarTop.style.height = `${result}px`;
            }
            if (!navbarBottom.classList.contains(requiredClass[0])) {
                navbarBottom.classList.add(...requiredClass);
                navbarBottom.animate([
                    { transform: 'translateY(-10vh)' },
                    { transform: 'translateY(0)' },
                ], {
                    duration: 300,
                    easing: 'ease-out',
                });
            }
        } else if (window.scrollY < (navbarBottom.offsetHeight * 2) + offset) {
            navbarTop.style.height = `${navbarTopBaseHeight}px`;
            navbarBottom.classList.remove(...requiredClass);
        }
    };
    window.addEventListener('scroll', onWindowScroll);
    window.addEventListener('DOMContentLoaded', onWindowScroll);
}

const sidebarInit = () => {
    const el = document.querySelector('.sidebar-mobile');
    const bg = el.querySelectorAll('.bg')
    const sidebarToggle = document.querySelectorAll('.toggle-sidebar-mobile')
    if (!el) return false;

    const toggle = () => {
        if (el.classList.contains('fixed')) {
            document.body.classList.remove('overflow-hidden');
            el.querySelector('button').animate([
                { transform: 'translateY(0)', opacity: 1 },
                { transform: 'translateY(200px)', opacity: 0 },
            ], {
                duration: 300,
                easing: 'ease-out',
            });
            el.querySelector('.sidebar-menu').animate([
                { transform: 'translateX(0)' },
                { transform: 'translateX(-300px)' },
            ], {
                duration: 300,
                easing: 'ease-out'
            }).onfinish = () => {
                el.classList.remove('fixed');
                el.classList.add('hidden');
            }
        } else {
            document.body.classList.add('overflow-hidden');
            el.classList.add('fixed');
            el.classList.remove('hidden');
            el.querySelector('.sidebar-menu').animate([
                { transform: 'translateX(-300px)' },
                { transform: 'translateX(0)' },
            ], {
                duration: 300,
                easing: 'ease-out'
            })
            el.querySelector('button').animate([
                { transform: 'translateY(200px)', opacity: 0 },
                { transform: 'translateY(0)', opacity: 1 },
            ], {
                duration: 300,
                easing: 'ease-out',
            })
        }
    }

    bg.forEach(el => el.addEventListener('click', toggle))
    sidebarToggle.forEach(el => el.addEventListener('click', toggle))

    const categoriesToggle = document.querySelectorAll('.kategori-toggle')
    const categoryToggle = () => {
        const ulMain = el.querySelector('ul.main');
        const ulCategory = el.querySelector('ul.kategori');

        if (ulMain.classList.contains('hidden')) {
            ulMain.classList.remove('hidden');
            ulCategory.classList.add('hidden');
        } else {
            ulMain.classList.add('hidden');
            ulCategory.classList.remove('hidden');
        }
    }
    categoriesToggle.forEach(el => el.addEventListener('click', categoryToggle))
}

const liveChatInit = () => {
    const el = document.querySelector('.floating-chat');
    const btnClose = el.querySelector('.btn-close');
    const messages = el.querySelector('.messages');
    const input = el.querySelector('.text-box');
    let firstOpened = true;

    // 
    Bots.push({
        "name": "bot",
        "precatch": function(text) {
            return text;
        },
        "actions": [
            {
                "catch": ["*halo*"],
                "response": "Halo Juga!"
            },
            {
                "catch": ["*nama toko*"],
                "response": "Nama Toko Ini adalah <strong>Ilham Camera</strong>"
            },
            {
                "catch": ["*alamat*"],
                "response": "Alamat Toko Ini di <strong>Jl. Raya Gayaman No.164, Gayaman, Kec. Mojoanyar, Mojokerto, Jawa Timur 61364</strong>"
            },
        ],
        "postcatch": function(text) {
            return text;
        },
        "functions": {},
        "synonyms": [],
        "variables": {}
    });
    // toggle
    const toggle = () => {
        if (el.classList.contains('opened')) {
            el.classList.remove('opened');
        } else {
            el.classList.add('opened');
        }
    }
    el.addEventListener('click', function () {
        if (!this.classList.contains('opened')) {
            setTimeout(() => {
                el.classList.add('opened')
                if (firstOpened) {
                    firstOpened = false;
                    setTimeout(() => {
                        commit('Halo selamat datang di Toko Kami!');
                    }, 500)
                }
            }, 150);
        }
    })
    btnClose.addEventListener('click', () => setTimeout(() => el.classList.remove('opened'), 150));

    // 
    input.addEventListener('keyup', (e) => {
        if (e.keyCode === 13) {
            const text = input.value;
            commit(text, false);
            input.value = '';
        
            setTimeout(() => {
                // 
                var r = Bots.sendMessage("bot", text, { "USER": 'self' });
                if (r.length > 0) {
                    for (var i = 0; i < r.length; i++) {
                        commit(r[i]);
                    }
                } else {
                    commit(`
                        Maaf, aku tidak mengerti maksudmu. Kamu dapat
                        <a href="https://wa.me/0895337617550" style="text-decoration: underline;">link ini</a>
                        ini untuk menghubungi WhatsApp Toko.
                    `)
                }
            }, Math.random() * 1000)
        }
    })
    

    // commit
    const commit = (msg, a = true) => {
        const newMsg = (msg instanceof HTMLElement) ? msg : document.createElement('li');

        if (msg instanceof HTMLElement) {
            
        } else {
            newMsg.innerHTML = msg;
        }

        newMsg.classList.add( a ? 'other' : 'self' );
        messages.appendChild(newMsg)

        // animate scroll messages to bottom
        messages.scrollTop = messages.scrollHeight;
    }
}

// 
navbarInit();
sidebarInit();
liveChatInit();