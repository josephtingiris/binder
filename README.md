# binder

PHP IPAM

Custom Subnet Fields

Dns_Update              - boolean 0     - Required - Should DNS updates be maintained for this subnet?
Dns_Forward             - boolean 0     - Optional - Should DNS updates be forwarded to the Dns_Forward_Server for this subnet?
Dns_Forward_Key         - varchar 128   - Optional - The key to use for updating the Dns_Forward_Server.
Dns_Forward_Servers     - varchar 128   - Optional - The IP address(es) of the Dns_Forward_Servers (separated by semi-colons).
Dns_Private             - boolean 0     - Optional - Should DNS updates be forwarded to the Dns_Private_Servers for this subnet?
Dns_Private_Key         - varchar 128   - Optional - The key to use for updating the Dns_Private_Servers.
Dns_Private_Servers     - varchar 128   - Optional - The IP address(es) of the Dns_Private_Servers (separated by semi-colons).
Dns_Public              - boolean 0     - Optional - Should DNS updates be forwarded to the Dns_Public_Servers for this subnet?
Dns_Public_Key          - varchar 128   - Optional - The key to use for updating the Dns_Public_Servers.
Dns_Public_Servers      - varchar 128   - Optional - The IP address(es) of the Dns_Public_Servers (separated by semi-colons).
Dns_Zone_Default        - varchar 128   - Optional - For unqualified names. what zone (domain) name should be appended to the host name?
