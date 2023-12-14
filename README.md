# zzh

<a name="introduction"></a>
## Introduction

zzh is a SSH client and connection manager which stores your hosts information, credentials to connect in your favorite termina. You don't need to remember your hosts information anymore.

<a name="installation"></a>
## Installation

zzh runs as a single binary and can be installed in different ways.

<a name="install-via-bash-script-linux-mac"></a>
### Install via bash script (Linux & Mac)

Linux & Mac users can install it directly to `/usr/local/bin/zzh` with:

```bash
sudo bash < <(curl -sL https://raw.githubusercontent.com/cuonggt/zzh/master/install.sh)
```

<a name="download-static-binary-linux-mac"></a>
### Download static binary (Linux and Mac)

Static binaries can always be found on the [releases](https://github.com/cuonggt/zzh/releases/latest). The `zzh` binary can extracted and copied to your `$PATH`, or simply run as `./zzh`.

<a name="compile-from-source"></a>
### Compile from source

Go (>= version 1.20) is required to compile zzh from source.

```shell
git clone git@github.com:cuonggt/zzh.git
cd zzh
```

Build the zzh binary:

```shell
go build -ldflags "-s -w"
mv zzh /usr/local/bin/zzh
```
