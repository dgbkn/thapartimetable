{ pkgs, legacyPolygott }: {
	deps = [
		pkgs.php80
	] ++ legacyPolygott;
}