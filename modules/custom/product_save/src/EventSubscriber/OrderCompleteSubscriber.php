<?php

namespace Drupal\product_save\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_order\Entity\Order;
use Drupal\user\Entity\User;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
/**
 * Class OrderCompleteSubscriber.
 *
 * @package Drupal\MY_MODULE
 */
class OrderCompleteSubscriber implements EventSubscriberInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructor.
   */
  public  function __construct($entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events['commerce_order.place.post_transition'] = ['orderCompleteHandler'];

    return $events;
  }

  /**
   * This method is called whenever the commerce_order.place.post_transition event is
   * dispatched.
   *
   * @param WorkflowTransitionEvent $event
   */
  public function orderCompleteHandler(WorkflowTransitionEvent $event) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $event->getEntity();
    // Order items in the cart.
    $items = $order->getItems();
    $user = \Drupal::currentUser();
    $currentUser = $user->id();
    foreach ($order->getItems() as $order_item) {
     // SetAdjustment to empty initially.
     $order_item->setAdjustments([]);
     $product_variation = $order_item->getPurchasedEntity();
     $did = $order_item->get('order_id')->getValue();
     $title = $order_item->getTitle();
     if (!empty($product_variation)) {
       $product_id = $product_variation->get('product_id')
         ->getValue()[0]['target_id'];
       $product = Product::load($product_id);
       $product_type = $product->get('type')->getValue()[0]['target_id'];
       $quantity = $order_item->getQuantity();
       $product_title = $product->getTitle();       
       $target_ids = $product->get('field_purchased_user')->getValue();  
        if($title == $product_title){    
         $product->get('field_purchased_user')->appendItem($currentUser);
         $product->save();        
        } 
      }
    }
  } 


  public function Permission(Request $request) {

    $result = \Drupal::entityQuery('commerce_product')
              ->condition('type', 'themes')
              ->execute();
    $path   = \Drupal::request()->getpathInfo();
    $arg    = explode('/',$path);    
    $user   = \Drupal::currentUser();
    $currentUser = $user->id();
    foreach ($result as $value) {
      $product = Product::load($value); 
      $product_title = $product->getTitle();
      $path_value = str_replace('-', ' ', strtolower($arg[2]));
      $title = strtolower($product_title);
      if ($path_value == $title){
        $target_ids = $product->get('field_purchased_user')->getValue();
        $user_ids = array_column($target_ids, 'target_id');          
        if (in_array($currentUser, $user_ids)) {
          return AccessResult::forbidden();
        }   
      }
    }     

      $response = new AjaxResponse();
      $response->addCommand(new AlertCommand(t('This is from an ajax callback')));
    return $response;

  }
  public function Render() {
   $result = \Drupal::entityQuery('commerce_product')
              ->condition('type', 'themes')
              ->execute();
    $path   = \Drupal::request()->getpathInfo();
    $arg    = explode('/',$path);    
    $user   = \Drupal::currentUser();
    $currentUser = $user->id();
    foreach ($result as $value) {
      $product = Product::load($value); 
      $product_title = $product->getTitle();
      $path_value = str_replace('-', ' ', strtolower($arg[2]));
      $title = strtolower($product_title);
      if ($path_value == $title){
        $target_ids = $product->get('field_purchased_user')->getValue();
        $user_ids = array_column($target_ids, 'target_id');          
        if (in_array($currentUser, $user_ids)) {
          print 'test';
        }   
      }
    }     

      $response = new AjaxResponse();
      $response->addCommand(new AlertCommand(t('This is from an ajax callback')));
    return $response;

}
}