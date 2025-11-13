.PHONY: install cs-check cs-fix psalm test

install:
	composer install

cs-check:
	./vendor/bin/phpcs

cs-fix:
	./vendor/bin/phpcbf
	./vendor/bin/php-cs-fixer fix

psalm:
	./vendor/bin/psalm

test:
	./bin/phpunit