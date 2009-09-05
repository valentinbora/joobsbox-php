#!/bin/sh
phpcs --report=xml --report-file=log/phpcs.xml ../JoobsBox/Model/ ../JoobsBox/Controllers/ ../JoobsBox/Helpers/ ../JoobsBox/Iterator/ ../JoobsBox/Plugin/
