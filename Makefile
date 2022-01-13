# Files to generate info.html
ONE_FILE_DOC = docs/info.php docs/CHANGELOG.md docs/database.md

install: composer.phar
	php composer.phar install

# Regenerate all documentation
doc: $(ONE_FILE_DOC) Doxyfile
	php docs/info.php > docs/info.html
	doxygen Doxyfile

# Regenerate one-file documentation
info.html: $(ONE_FILE_DOC)
	echo "Regenerating info.html"
	php docs/info.php > docs/info.html

# Regenerate code documentation
code_doc: Doxyfile
	echo "Regenerating documentation"
	doxygen Doxyfile

# Regenerate minified JS&CSS
minify: src/js/ src/css/ src/themes/
	echo "Minifying JS..."
	php -r "require 'src/php/minifier.php'; minifyAllJS();"
	echo "Minifying CSS..."
	php -r "require 'src/php/minifier.php'; minifyAllCSS();"
	echo "Regenerating themes..."
	php -r "require 'src/php/minifier.php'; regenerateThemes();"

# Do not minify for development version!
no-minify: src/js/ src/css/ src/themes/
	cp -r src/js/ .
	cp src/js/third_party/* js/
	rm -rf js/third_party
	cp -r src/css .
	cp -r src/themes/ .

# Clear documentation
clean: 
	rm docs/info.html
	rm -rf docs/html