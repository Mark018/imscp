<?php
/**
 * i-MSCP - internet Multi Server Control Panel
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/***********************************************************************************************************************
 * Script functions
 */

/**
 * Generate List of Domains assigned to IPs
 *
 * @param  TemplateEngine $tpl Template engine
 * @return void
 */
function listIPDomains($tpl)
{
	$resellerId = $_SESSION['user_id'];

	$stmt = exec_query('SELECT reseller_ips FROM reseller_props WHERE reseller_id = ?', $resellerId);
	$data = $stmt->fetch();
	$resellerIps = explode(';', substr($data['reseller_ips'], 0, -1));

	$stmt = execute_query('SELECT ip_id, ip_number FROM server_ips WHERE ip_id IN (' . implode(',', $resellerIps) . ')');

	while ($ip = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$stmt2 = exec_query(
			'
				SELECT
					domain_name
				FROM
					domain
				INNER JOIN
					admin ON(admin_id = domain_admin_id)
				WHERE
					domain_ip_id = :ip_id
				AND
					created_by = :reseller_id
				UNION
				SELECT
					alias_name AS domain_name
				FROM
					domain_aliasses
				INNER JOIN
					domain USING(domain_id)
				INNER JOIN
					admin ON(admin_id = domain_admin_id)
				WHERE
					alias_ip_id = :ip_id
				AND
					created_by = :reseller_id
			',
			array('ip_id' => $ip['ip_id'], 'reseller_id' => $resellerId)
		);

		$domainsCount = $stmt2->rowCount();

		$tpl->assign(
			array(
				'IP' => tohtml($ip['ip_number']),
				'RECORD_COUNT' => tr('Total Domains') . ': ' . ($domainsCount)
			)
		);

		if ($domainsCount) {
			while ($data = $stmt2->fetch(PDO::FETCH_ASSOC)) {
				$tpl->assign('DOMAIN_NAME', tohtml(idn_to_utf8($data['domain_name'])));
				$tpl->parse('DOMAIN_ROW', '.domain_row');
			}
		} else {
			$tpl->assign('DOMAIN_NAME', tr('No used yet'));
			$tpl->parse('DOMAIN_ROW', 'domain_row');
		}

		$tpl->parse('IP_ROW', '.ip_row');
		$tpl->assign('DOMAIN_ROW', '');
	}
}

/***********************************************************************************************************************
 * Main script
 */

require '../../application.php';

\iMSCP\Core\Application::getInstance()->getEventManager()->trigger(\iMSCP\Core\Events::onResellerScriptStart);

check_login('reseller');

if (resellerHasCustomers()) {
	$cfg = \iMSCP\Core\Application::getInstance()->getConfig();

	/** @var $tpl TemplateEngine */
	$tpl = new \iMSCP\Core\Template\TemplateEngine();

	$tpl->define_dynamic(array(
		'layout' => 'shared/layouts/ui.tpl',
		'page' => 'reseller/ip_usage.tpl',
		'page_message' => 'layout',
		'ip_row' => 'page',
		'domain_row' => 'ip_row'
	));

	$reseller_id = $_SESSION['user_id'];

	$tpl->assign(array(
		'TR_PAGE_TITLE' => tr('Reseller / Statistics / IP Usage'),
		'TR_DOMAIN_STATISTICS' => tr('Domain statistics'),
		'TR_IP_RESELLER_USAGE_STATISTICS' => tr('Reseller/IP usage statistics'),
		'TR_DOMAIN_NAME' => tr('Domain Name')
	));

	generateNavigation($tpl);
	generatePageMessage($tpl);
	listIPDomains($tpl);

	$tpl->parse('LAYOUT_CONTENT', 'page');

	\iMSCP\Core\Application::getInstance()->getEventManager()->trigger(\iMSCP\Core\Events::onResellerScriptEnd, array('templateEngine' => $tpl));

	$tpl->prnt();

	unsetMessages();
} else {
	showBadRequestErrorPage();
}
