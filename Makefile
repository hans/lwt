# File for Doxygen
DOC_DIRECTIONS = Doxyfile
# Files to generate info.html
ONE_FILE_DOC = docs/info.php docs/CHANGELOG.md

install: composer.phar
	php composer.phar install

# Regenerate all documentation
doc: $(ONE_FILE_DOC) $(DOC_DIRECTIONS)
	php docs/info.php > docs/info.html
	doxygen Doxyfile

# Regenerate one-file documentation
info.html: $(ONE_FILE_DOC)
	echo "Regenerating info.html"
	php docs/info.php > docs/info.html

# Regenerate code documentation
code_doc: $(DOC_DIRECTIONS)
	echo "Regenerating documentation"
	doxygen Doxyfile

# Clear documentation
clean: 
	rm docs/info.html
	rm -rf docs/html