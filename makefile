M ?= upload local

upd:
	git add -A && git commit -m "${M}" && git pull && git push