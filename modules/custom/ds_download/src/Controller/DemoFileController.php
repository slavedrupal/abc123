<?php

namespace Drupal\ds_download_file\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\commerce_product\Entity\Product;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Aws\S3\S3Client;


/**
 * Controller routines for AJAX example routes.
 */
class DemoFileController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'ds_download_file';
  }

  public function DemoFilePermission(AccountInterface $account, $theme_id) {
    $currentUser = $account->id();
    $result = \Drupal::entityQuery('commerce_product')
              ->condition('type', 'themes')
              ->condition('product_id', $theme_id)
              ->condition('field_purchased_user', $currentUser, 'IN' )
              ->execute();
    if (empty($result))
      return AccessResult::forbidden();
    else
      return AccessResult::allowed();
  }

  /**
   * Callback for link.
   *
   */
  public function LinkCallback($theme_id) {

    $theme_raw_name = str_replace('-', ' ', $theme_name);
    $result = \Drupal::entityQuery('commerce_product')
              ->condition('type', 'themes')
              ->condition('product_id', $theme_id)
              ->execute();
    $nid = reset($result);
    $entity = \Drupal::entityTypeManager()->getStorage('commerce_product')->load($theme_id);
    $demo_file = $entity->field_backupfile_name->value;
    
    try {
        // Force download
        $remoteURL='u947684974.drupalslave.com/themes/'.$demo_file;
        header("Content-Disposition: attachment; filename=$demo_file");
        ob_end_clean();
        readfile($remoteURL);
        flush();
        
        $dcount=$entity->get('field_download_count')->value;
        if($dcount=='')
          $entity->set('field_download_count',1);
        else{
          $dcount++;
          $entity->set('field_download_count',$dcount);
        }
        $entity->save();
        return;
        
    } catch (Exception $e) {
    }
  }
}
