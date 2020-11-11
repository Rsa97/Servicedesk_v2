const routes = [
  {
    path: '/',
    component: () => import('pages/Desktop.vue'),
  },
  {
    path: '/info/:class',
    component: () => import('pages/ClassInfo.vue'),
  },
];

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue'),
  });
}

export default routes;
