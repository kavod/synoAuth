synoAuth.spk: package.tgz INFO scripts runMD5
	tar cvf $@ package.tgz INFO scripts

runMD5:
	./checksum

package.tgz:application
	tar zcvf $@ application
