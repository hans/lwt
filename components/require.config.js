var components = {
    "packages": [
        {
            "name": "jquery",
            "main": "jquery-built.js"
        },
        {
            "name": "jquery.scrollto",
            "main": "jquery.scrollto-built.js"
        },
        {
            "name": "jplayer",
            "main": "jplayer-built.js"
        }
    ],
    "shim": {
        "jplayer": {
            "deps": [
                "jquery"
            ]
        }
    },
    "baseUrl": "components"
};
if (typeof require !== "undefined" && require.config) {
    require.config(components);
} else {
    var require = components;
}
if (typeof exports !== "undefined" && typeof module !== "undefined") {
    module.exports = components;
}