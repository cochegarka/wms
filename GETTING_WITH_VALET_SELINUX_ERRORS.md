ref. to https://cpriego.github.io/valet-linux/requirements#fedora:

Fedora users are expected to have knowledge of SELinux and how to configure or disable it while Valet makes changes to the system files, otherwise you will receive errors about changes that could not be made.

The easiest way is to set SELinux in Permissive mode.
How to set SELinux in Permissive Mode

Temporarily (until reboot): sudo setenforce 0
