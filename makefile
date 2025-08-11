M ?= upload local

upd:
	git add -A && git commit -m "upload $(date '+%Y-%m-%d %H:%M:%S')" && git pull && git push