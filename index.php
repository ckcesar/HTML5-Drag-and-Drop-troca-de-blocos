<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Função Arrastar e soltar nativa do HTML5 - HTML5 Rocks</title>

    <style>
        figure img { border: 1px solid #ccc; }
        h1,h2,h3,h4 { clear: both; }
        /* Prevent the contents of draggable elements from being selectable. */
        [draggable] {
            -moz-user-select: none;
            -khtml-user-select: none;
            -webkit-user-select: none;
            user-select: none;
            /* Required to make elements draggable in old WebKit */
            -khtml-user-drag: element;
            -webkit-user-drag: element;
        }
        dd {
            padding: 5px 0;
        }
        .column {
            height: 150px;
            width: 150px;
            float: left;
            border: 2px solid #666666;
            background-color: #ccc;
            margin-right: 5px;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            -o-border-radius: 10px;
            -ms-border-radius: 10px;
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 3px #000;
            -moz-box-shadow: inset 0 0 3px #000;
            -ms-box-shadow: inset 0 0 3px #000;
            -o-box-shadow: inset 0 0 3px #000;
            box-shadow: inset 0 0 3px #000;
            text-align: center;
            cursor: move;
            margin-bottom: 30px;
        }
        .column header {
            color: #fff;
            text-shadow: #000 0 1px;
            box-shadow: 5px;
            padding: 5px;
            background: -moz-linear-gradient(left center, rgb(0,0,0), rgb(79,79,79), rgb(21,21,21));
            background: -webkit-gradient(linear, left top, right top,
            color-stop(0, rgb(0,0,0)),
            color-stop(0.50, rgb(79,79,79)),
            color-stop(1, rgb(21,21,21)));
            background: -webkit-linear-gradient(left center, rgb(0,0,0), rgb(79,79,79), rgb(21,21,21));
            background: -ms-linear-gradient(left center, rgb(0,0,0), rgb(79,79,79), rgb(21,21,21));
            background: -o-linear-gradient(left center, rgb(0,0,0), rgb(79,79,79), rgb(21,21,21));
            border-bottom: 1px solid #ddd;
            -webkit-border-top-left-radius: 10px;
            -moz-border-radius-topleft: 10px;
            -ms-border-radius-topleft: 10px;
            -o-border-radius-topleft: 10px;
            border-top-left-radius: 10px;
            -webkit-border-top-right-radius: 10px;
            -moz-border-radius-topright: 10px;
            -ms-border-radius-topright: 10px;
            -o-border-radius-topright: 10px;
            border-top-right-radius: 10px;
        }
        #columns-full .column {
            -webkit-transition: -webkit-transform 0.2s ease-out;
            -moz-transition: -moz-transform 0.2s ease-out;
            -o-transition: -o-transform 0.2s ease-out;
            -ms-transition: -ms-transform 0.2s ease-out;
        }
        #columns-full .column.over,
        #columns-dragOver .column.over,
        #columns-dragEnd .column.over,
        #columns-almostFinal .column.over {
            border: 2px dashed #000;
        }
        #columns-full .column.moving {
            opacity: 0.25;
            -webkit-transform: scale(0.8);
            -moz-transform: scale(0.8);
            -ms-transform: scale(0.8);
            -o-transform: scale(0.8);
        }
        #columns-full .column .count {
            padding-top: 15px;
            font-weight: bold;
            text-shadow: #fff 0 1px;
        }
    </style>

</head>
<body>
    <p>Só Arrastar os blocos</p>
    <div id="columns-almostFinal">
      <div class="column"><header>C</header></div>
      <div class="column"><header>K</header></div>
      <div class="column"><header>P</header></div>
    </div>




                <script>
                    // Using this polyfill for safety.
                    Element.prototype.hasClassName = function(name) {
                        return new RegExp("(?:^|\\s+)" + name + "(?:\\s+|$)").test(this.className);
                    };

                    Element.prototype.addClassName = function(name) {
                        if (!this.hasClassName(name)) {
                            this.className = this.className ? [this.className, name].join(' ') : name;
                        }
                    };

                    Element.prototype.removeClassName = function(name) {
                        if (this.hasClassName(name)) {
                            var c = this.className;
                            this.className = c.replace(new RegExp("(?:^|\\s+)" + name + "(?:\\s+|$)", "g"), "");
                        }
                    };


                    var samples = samples || {};

                    // dragStart
                    (function() {
                        var id_ = 'columns-dragStart';
                        var cols_ = document.querySelectorAll('#' + id_ + ' .column');

                        this.handleDragStart = function(e) {
                            e.dataTransfer.effectAllowed = 'move';
                            e.dataTransfer.setData('text/html', 'blah'); // needed for FF.

                            // Target element (this) is the source node.
                            this.style.opacity = '0.4';
                        };

                        [].forEach.call(cols_, function (col) {
                            // Enable columns to be draggable.
                            col.setAttribute('draggable', 'true');
                            col.addEventListener('dragstart', this.handleDragStart, false);
                        });

                    })();

                    // dragEnd
                    (function() {
                        var id_ = 'columns-dragEnd';
                        var cols_ = document.querySelectorAll('#' + id_ + ' .column');

                        this.handleDragStart = function(e) {
                            e.dataTransfer.effectAllowed = 'move';
                            e.dataTransfer.setData('text/html', this.innerHTML); // needed for FF.

                            // Target element (this) is the source node.
                            this.style.opacity = '0.4';
                        };

                        this.handleDragOver = function(e) {
                            if (e.preventDefault) {
                                e.preventDefault(); // Allows us to drop.
                            }

                            e.dataTransfer.dropEffect = 'move';

                            return false;
                        };

                        this.handleDragEnter = function(e) {
                            this.addClassName('over');
                        };

                        this.handleDragLeave = function(e) {
                            // this/e.target is previous target element.
                            this.removeClassName('over');
                        };

                        this.handleDragEnd = function(e) {
                            [].forEach.call(cols_, function (col) {
                                col.removeClassName('over');
                            });

                            // target element (this) is the source node.
                            this.style.opacity = '1';
                        };

                        [].forEach.call(cols_, function (col) {
                            // Enable columns to be draggable.
                            col.setAttribute('draggable', 'true');
                            col.addEventListener('dragstart', this.handleDragStart, false);
                            col.addEventListener('dragenter', this.handleDragEnter, false);
                            col.addEventListener('dragover', this.handleDragOver, false);
                            col.addEventListener('dragleave', this.handleDragLeave, false);
                            col.addEventListener('dragend', this.handleDragEnd, false);
                        });

                    })();

                    // dragIcon
                    (function() {
                        var id_ = 'columns-dragIcon';
                        var cols_ = document.querySelectorAll('#' + id_ + ' .column');

                        this.handleDragStart = function(e) {
                            e.dataTransfer.effectAllowed = 'move';
                            e.dataTransfer.setData('text/html', this.innerHTML);

                            var dragIcon = document.createElement('img');
                            dragIcon.src = '/static/images/google_logo_small.png';
                            e.dataTransfer.setDragImage(dragIcon, -10, -10);

                            // Target element (this) is the source node.
                            this.style.opacity = '0.4';
                        };

                        this.handleDragLeave = function(e) {
                            // this/e.target is previous target element.

                            this.removeClassName('over');
                        };

                        this.handleDragEnd = function(e) {
                            // this/e.target is the source node.

                            this.style.opacity = '1';

                            [].forEach.call(cols_, function (col) {
                                col.removeClassName('over');
                            });
                        };

                        [].forEach.call(cols_, function (col) {
                            // Enable columns to be draggable.
                            col.setAttribute('draggable', 'true');
                            col.addEventListener('dragstart', this.handleDragStart, false);
                            col.addEventListener('dragend', this.handleDragEnd, false);
                            col.addEventListener('dragleave', this.handleDragLeave, false);
                        });

                    })();

                    // Almost final example
                    (function() {
                        var id_ = 'columns-almostFinal';
                        var cols_ = document.querySelectorAll('#' + id_ + ' .column');
                        var dragSrcEl_ = null;

                        this.handleDragStart = function(e) {
                            e.dataTransfer.effectAllowed = 'move';
                            e.dataTransfer.setData('text/html', this.innerHTML);

                            dragSrcEl_ = this;

                            this.style.opacity = '0.4';

                            // this/e.target is the source node.
                            this.addClassName('moving');
                        };

                        this.handleDragOver = function(e) {
                            if (e.preventDefault) {
                                e.preventDefault(); // Allows us to drop.
                            }

                            e.dataTransfer.dropEffect = 'move';

                            return false;
                        };

                        this.handleDragEnter = function(e) {
                            this.addClassName('over');
                        };

                        this.handleDragLeave = function(e) {
                            // this/e.target is previous target element.

                            this.removeClassName('over');
                        };

                        this.handleDrop = function(e) {
                            // this/e.target is current target element.

                            if (e.stopPropagation) {
                                e.stopPropagation(); // stops the browser from redirecting.
                            }

                            // Don't do anything if we're dropping on the same column we're dragging.
                            if (dragSrcEl_ != this) {
                                dragSrcEl_.innerHTML = this.innerHTML;
                                this.innerHTML = e.dataTransfer.getData('text/html');
                            }

                            return false;
                        };

                        this.handleDragEnd = function(e) {
                            // this/e.target is the source node.
                            this.style.opacity = '1';

                            [].forEach.call(cols_, function (col) {
                                col.removeClassName('over');
                                col.removeClassName('moving');
                            });
                        };

                        [].forEach.call(cols_, function (col) {
                            col.setAttribute('draggable', 'true');  // Enable columns to be draggable.
                            col.addEventListener('dragstart', this.handleDragStart, false);
                            col.addEventListener('dragenter', this.handleDragEnter, false);
                            col.addEventListener('dragover', this.handleDragOver, false);
                            col.addEventListener('dragleave', this.handleDragLeave, false);
                            col.addEventListener('drop', this.handleDrop, false);
                            col.addEventListener('dragend', this.handleDragEnd, false);
                        });
                    })();

                    // Full example
                    (function() {
                        var id_ = 'columns-full';
                        var cols_ = document.querySelectorAll('#' + id_ + ' .column');
                        var dragSrcEl_ = null;

                        this.handleDragStart = function(e) {
                            e.dataTransfer.effectAllowed = 'move';
                            e.dataTransfer.setData('text/html', this.innerHTML);

                            dragSrcEl_ = this;

                            // this/e.target is the source node.
                            this.addClassName('moving');
                        };

                        this.handleDragOver = function(e) {
                            if (e.preventDefault) {
                                e.preventDefault(); // Allows us to drop.
                            }

                            e.dataTransfer.dropEffect = 'move';

                            return false;
                        };

                        this.handleDragEnter = function(e) {
                            this.addClassName('over');
                        };

                        this.handleDragLeave = function(e) {
                            // this/e.target is previous target element.
                            this.removeClassName('over');
                        };

                        this.handleDrop = function(e) {
                            // this/e.target is current target element.

                            if (e.stopPropagation) {
                                e.stopPropagation(); // stops the browser from redirecting.
                            }

                            // Don't do anything if we're dropping on the same column we're dragging.
                            if (dragSrcEl_ != this) {
                                dragSrcEl_.innerHTML = this.innerHTML;
                                this.innerHTML = e.dataTransfer.getData('text/html');

                                // Set number of times the column has been moved.
                                var count = this.querySelector('.count');
                                var newCount = parseInt(count.getAttribute('data-col-moves')) + 1;
                                count.setAttribute('data-col-moves', newCount);
                                count.textContent = 'moves: ' + newCount;
                            }

                            return false;
                        };

                        this.handleDragEnd = function(e) {
                            // this/e.target is the source node.
                            [].forEach.call(cols_, function (col) {
                                col.removeClassName('over');
                                col.removeClassName('moving');
                            });
                        };

                        [].forEach.call(cols_, function (col) {
                            col.setAttribute('draggable', 'true');  // Enable columns to be draggable.
                            col.addEventListener('dragstart', this.handleDragStart, false);
                            col.addEventListener('dragenter', this.handleDragEnter, false);
                            col.addEventListener('dragover', this.handleDragOver, false);
                            col.addEventListener('dragleave', this.handleDragLeave, false);
                            col.addEventListener('drop', this.handleDrop, false);
                            col.addEventListener('dragend', this.handleDragEnd, false);
                        });
                    })();
                </script>
</body>
</html>