var autoplaceBodyAds = function () {
    var lbsList = ['nn_lb3', 'nn_lb4', 'nn_lb5', 'nn_lb6'];
    var createNewInsert = function (id, domElement) {
        var parentDiv = domElement.parentNode;
        var div = document.createElement('div');
        div.setAttribute('id', id);
        div.setAttribute('style', 'text-align:center;');
        parentDiv.insertBefore(div, domElement);
    };

    var getPostContentDiv = function () {
        var postContent = document.getElementById('content');
        if (postContent) {
            return postContent.querySelector('.post-content');
        }
        return null;
    }

    var getPlacementSequence = function (noOfH3) {
        if (noOfH3) {
            if (noOfH3 > 10) {
                return 3;
            }
            if (noOfH3 > 8 && noOfH3 < 10) {
                return 2;
            }
        }
        return 1;
    }

    var startInserting = function(h3s) {
        var noOfH3 = h3s.length;
        if (noOfH3 > 2) {
            var h3PlacementIndex = 1;
            var placementSequence = getPlacementSequence(noOfH3);
            for (var h3LoopIndex = 0; h3LoopIndex < noOfH3; h3LoopIndex++) {
                if (lbsList[h3LoopIndex] && h3s[h3PlacementIndex]) {
                    createNewInsert(lbsList[h3LoopIndex], h3s[h3PlacementIndex]);
                }
                h3PlacementIndex += placementSequence;
            }
        }
    }

    var postContent = getPostContentDiv();
    if (postContent) {
        var h3s = postContent.querySelectorAll("h3");
        if (h3s) {
            startInserting(h3s);
        }
    }
};

jQuery(document).ready(function () {
    if (autoplaceBodyAdsVars && !autoplaceBodyAdsVars.isHomePage) {
        autoplaceBodyAds();
    }
});