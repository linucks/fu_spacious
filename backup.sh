#!/bin/bash -x

# Inspired by: http://www.mikerubel.org/computers/rsync_snapshots/
# Also: https://uk.godaddy.com/help/backup-mysql-databases-on-your-server-linux-17547

function rsync_bkup {
    root=alexprob@farmurban.co.uk:/home/alexprob/public_html/farmurban/
    fname=$1
    slash=$2
    [[ -e $fname.3 ]] && rm -rf $fname.3
    [[ -e $fname.2 ]] && mv $fname.2 $fname.3
    ldest=""
    if [ -e $fname.1 ]; then
        if [ -d $fname.1 ]; then
            ldest="--link-dest=../$fname.1"
        fi
        [[ -e $fname.0 ]] && mv $fname.1 $fname.2
    fi
    [[ -e $fname.0 ]] && mv $fname.0 $fname.1
    #rsync $SSH_ARGS -a --delete $ldest $root/${fname}${slash} $fname.0
    rsync -a --delete $ldest $root/${fname}${slash} $fname.0
}

# For this to work the ssh-agent needs to be running with the private key from id_sar
# use ssh-add id_rsa
# pw; ?ILAhmc99?
export SSH_AGENT_PID=10968
SSH_AUTH_SOCK=/tmp/ssh-RfsgGrUm4ve4/agent.10967

#export SSH_ARGS="-e ssh -i /media/data/shared/farmurban/id_rsa"

# wp-config.php shouldn't change so we don't bother backing this up.
rsync_bkup farmurbanwp13_dump.sql
rsync_bkup wp-content /
